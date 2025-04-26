<?php
error_reporting(E_ALL);

$host = "user.c6xqcw662dx5.us-east-1.rds.amazonaws.com";
$user = "admin";
$password = "5prin32025Gr0up";
$db = "comp424";

$connection = mysqli_connect($host, $user, $password, $db);
    if (!$connection) {
        echo json_encode(["success" => false, "message" => "Database connection error."]);
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
        
        $user = $_POST['username'] ?? '';
        $entered_code = $_POST['verify-code'] ?? '';
        if (empty($entered_code)) {
            echo json_encode(["success" => false, "message" => "Please enter your 6-digit code."]);
            exit();
        }
        
        $query = $connection->prepare("SELECT verified, verify_code FROM users WHERE username = ?");
        $query->bind_param("s", $user);
        $query->execute();
        $query->bind_result( $verified, $verification_code);
        $query->fetch();
        $query->close();

        if ($entered_code == $verification_code) {
            $updateQuery = $connection->prepare("UPDATE users SET verified = 1 WHERE username = ?");
            $updateQuery->bind_param("s", $user);
            $updateQuery->execute();
            $updateQuery->close();
        }

        else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Incorrect code."]);
        }
    }
    
    $connection->close();

?>