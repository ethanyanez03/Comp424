<?php
// Set correct content-type
header('Content-Type: application/json');

// Debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB connection
$servername = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "5prin32025Gr0up";
$dbname = "comp424";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit();
}

// Only process POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputUsername = $_POST['username'] ?? '';
    $inputPassword = $_POST['password'] ?? '';

    // Validate fields
    if (empty($inputUsername) || empty($inputPassword)) {
        echo json_encode(["status" => "error", "message" => "Username and password required."]);
        exit();
    }

    // Query user
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($inputPassword, $hashedPassword)) {
            echo json_encode(["status" => "success", "message" => "Login successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Incorrect password."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username not found."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
?>