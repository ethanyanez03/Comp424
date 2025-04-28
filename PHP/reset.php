<?php
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
$servername = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  echo json_encode(["success" => false, "message" => "Database connection failed."]);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old_password = $_POST['old-password'] ?? '';
  $new_password = $_POST['new-password'] ?? '';
  $email = $_SESSION['reset_email'];

  if (empty($old_password) || empty($new_password)) {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
    exit();
  }

  if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Can't recognize email attached to this password"]);
    exit();
  }

  $prep = $conn->prepare("SELECT password FROM users WHERE email = ?");
  $prep->bind_param("s", $email);
  $prep->execute();
  $prep->store_result();
  $prep->bind_result($stored_hash);
  $prep->fetch();

  if (($prep->num_rows < 1) || (!(password_verify($old_password, $stored_hash)))) {
    echo json_encode(["success" => false, "message" => "Please enter your old password."]);
    exit();
  }

  $hashed_new = password_hash($new_password, PASSWORD_DEFAULT);
  $query = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
  $query->bind_param("ss", $hashed_new, $email);
  $query->execute();

  if ($query->affected_rows > 0) {
    // Cleanup session data
    unset($_SESSION['reset_email']);
    echo json_encode(["success" => true, "message" => "Password reset."]);
  } else {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Problem resetting password."]);
  }
  $query->close();
}

$conn->close();
?>