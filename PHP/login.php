<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set("America/Los_Angeles");

// DB connection
$servername = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Database connection failed."]);
  exit();
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $inputUsername = $_POST['username'] ?? '';
  $inputPassword = $_POST['password'] ?? '';

  if (empty($inputUsername) || empty($inputPassword)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Username and password required."]);
    exit();
  }

  $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
  if (!$stmt) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database error."]);
    exit();
  }

  $stmt->bind_param("s", $inputUsername);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();

    if (password_verify($inputPassword, $hashedPassword)) {

        // Optional: Fetch full user info
        $userQuery = $conn->prepare("SELECT first_name, last_name, signed_in, verified FROM users WHERE username = ?");
        $userQuery->bind_param("s", $inputUsername);
        $userQuery->execute();
        $userQuery->bind_result($firstName, $lastName, $loginCount, $verified);
        $userQuery->fetch();
        $userQuery->close();

        if ($verified == 0) {
          echo json_encode(["success" => false, "message" => "Please verify your account before signing in."]);
          exit();
        }
        // Increment login count & update last login
        $newCount = $loginCount + 1;
        $today = date("Y-m-d");
        $updateStmt = $conn->prepare("UPDATE users SET signed_in = ?, last_login = ? WHERE username = ?");
        $updateStmt->bind_param("iss", $newCount, $today, $inputUsername);
        $updateStmt->execute();
        $updateStmt->close();
      
        $_SESSION['user'] = [
          "username" => $inputUsername,
          "first_name" => $firstName,
          "last_name" => $lastName,
          "login_count" => $newCount,
          "last_login" => $today
        ];

      header("Location: /PHP/home.php");
      exit();
    } else {
      http_response_code(401);
      echo json_encode(["success" => false, "message" => "Incorrect password."]);
    }
  } else {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Username not found."]);
  }

  $stmt->close();
} else {
  http_response_code(405);
  echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>