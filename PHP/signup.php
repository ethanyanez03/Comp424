<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo "Form submitted<br>";
  print_r($_POST);  // Show raw data
  $doc = null;
  try {
    $doc = new DomDocument;
    $doc->validateOnParse = true;
    $doc->load('../HTML/signup.html');
  }

  catch(Exception $err) {
    echo "Unknown file.";
  }

  $first_name = $_POST['first-name'];
  $last_name = $_POST['last-name'];
  $birth_date = $_POST['birth-date'];
  $email = $_POST['email'];
  $user = $_POST['username'];
  $pw = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $question = $_POST['security-question'];
  $answer = $_POST['security-answer'];
  $verify_code = $doc->getElementById('verify-code');

  $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, birth_date, security_question, security_answer, created_at, verify_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

  if (!$stmt) {
    die("Prepare failed: " . $conn->error);
  }

  $t = time();
  $stmt->bind_param("sssssssssi", $first_name, $last_name, $email, $user, $pw, $birth_date, $question, $answer, $t, $verify_code);

  if ($stmt->execute()) {
    echo "Signup successful!";
  } else {
    echo "Execute failed: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();
} else {
  echo "Not a POST request";
}
?>  

