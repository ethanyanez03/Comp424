<?php
// CORS Headers - allow frontend to talk to backend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  echo json_encode(["success" => false, "message" => "Database connection failed."]);
  exit();
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(["success" => false, "message" => "Invalid request method."]);
  exit();
}

// Get input
$inputUsername = $_POST['username'] ?? '';
$inputPassword = $_POST['password'] ?? '';

if (empty($inputUsername) || empty($inputPassword)) {
  echo json_encode(["success" => false, "message" => "Username and password are required."]);
  exit();
}

// Check username
$stmt = $conn->prepare("SELECT password, first_name, last_name, signed_in, verified FROM users WHERE username = ?");
if (!$stmt) {
  echo json_encode(["success" => false, "message" => "Database error."]);
  exit();
}

$stmt->bind_param("s", $inputUsername);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
  $stmt->bind_result($hashedPassword, $firstName, $lastName, $loginCount, $verified);
  $stmt->fetch();

  // Check if user is verified
  if ($verified == 0) {
    echo json_encode(["success" => false, "message" => "Please verify your account before logging in."]);
    exit();
  }

  // Check password
  if (!empty($hashedPassword) && password_verify($inputPassword, $hashedPassword)) {
    // Password correct - update login info
    $newCount = $loginCount + 1;
    $today = date("Y-m-d");

    $updateStmt = $conn->prepare("UPDATE users SET signed_in = ?, last_login = ? WHERE username = ?");
    $updateStmt->bind_param("iss", $newCount, $today, $inputUsername);
    $updateStmt->execute();
    $updateStmt->close();

    // Set session
    $_SESSION['user'] = [
      "username" => $inputUsername,
      "first_name" => $firstName,
      "last_name" => $lastName,
      "login_count" => $newCount,
      "last_login" => $today
    ];

    echo json_encode(["success" => true, "message" => "Login successful.", "username" => $inputUsername]);
  } else {
    echo json_encode(["success" => false, "message" => "Incorrect password."]);
  }

} else {
  echo json_encode(["success" => false, "message" => "Username not found."]);
}

$stmt->close();
$conn->close();
?>
