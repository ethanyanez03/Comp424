<?php
// Show errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to RDS
$servername = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Handle POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $input_user = $_POST['username'];
  $input_pw = $_POST['password'];

  // Look up user
  $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
  $stmt->bind_param("s", $input_user);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($hashed_pw);
    $stmt->fetch();

    // Verify password
    if (password_verify($input_pw, $hashed_pw)) {
      echo json_encode(["success" => true]);
    } else {
      echo json_encode(["success" => false, "message" => "Incorrect password."]);
    }
  } else {
    echo json_encode(["success" => false, "message" => "User not found."]);
  }

  $stmt->close();
} else {
  echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
?>