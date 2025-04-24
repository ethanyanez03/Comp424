<?php
// RDS connection settings
$servername = "authentication.c6xqcw662dx5.us-east-1.rds.amazonaws.com"; // ✅ replace with actual endpoint
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

// Connect to MySQL RDS
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Collect form data (sent via POST)
$first_name = $_POST['first-name'];
$last_name = $_POST['last-name'];
$email = $_POST['email'];
$user = $_POST['username'];
$pw = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure password
$question = $_POST['security-question'];
$answer = $_POST['security-answer'];

// Insert data using prepared statement
$stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $first_name, $last_name, $email, $user, $pw, $question, $answer);

if ($stmt->execute()) {
  echo "Signup successful!";
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

