<?php
header('Content-Type: application/json');
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
        
        $query = $connection->prepare("SELECT username, verified FROM users WHERE verify_code = ?");
        $query->bind_param("s", $entered_code);
        $query->execute();
        $query->bind_result( $user, $verified);
        $query->fetch();
        $query->close();

        if (($user != null) && ($verified < 1)) {
            $updateQuery = $connection->prepare("UPDATE users SET verified = 1, verify_code = DEFAULT WHERE username = ?");
            $updateQuery->bind_param("s", $user);
            $updateQuery->execute();
            $updateQuery->close();
            echo json_encode(["success" => true, "message" => "Verified."]);
        }

        else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Incorrect code."]);
        }
    }
    
    $connection->close();

?>