<?php
$servername = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

// Connect to MySQL RDS
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input from POST
$user = $_POST['username'];
$pass = $_POST['password'];

// Query the database for matching username
$stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (password_verify($pass, $hashed_password)) {
        echo "success";
    } else {
        echo "invalid"; // wrong password
    }
} else {
    echo "not found"; // no such user
}

$stmt->close();
$conn->close();
?>