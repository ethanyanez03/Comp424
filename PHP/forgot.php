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
        
        if(isset($_POST['user_email'])) {
            $email = $_POST['user_email'] ?? '';
            if (empty($email)) {
                echo json_encode(["success" => false, "message" => "Please enter your email."]);
                exit();
            }
        
            $query = $connection->prepare("SELECT username FROM users WHERE email = ?");
            $query->bind_param("s", $email);
            $query->execute();
            $query->bind_result( $username);

            if ($query->fetch()) {
                echo json_encode(["success" => true, "message" => "Found username.", "username" => $username]);
            }

            else {
                http_response_code(401);
                echo json_encode(["success" => false, "message" => "Unknown email."]);
            }
            
            $query->close();
        }
        
        else if (isset($_POST['reset_email'])) {
            $reset_email = $_POST['reset_email'] ?? '';
            if (empty($reset_email)) {
                echo json_encode(["success" => false, "message" => "Please enter your email."]);
                exit();
            }

            $q = $connection->prepare("SELECT password FROM users WHERE email = ?");
            $q->bind_param("s", $reset_email);
            $q->execute();
            $q->bind_result($oldPassword);

            if ($q->fetch()) {
                echo json_encode(["success" => true, "message" => "Found password to change.", "password" => $oldPassword]);
            }

            else {
                echo json_encode(["success" => false, "message" => "Unknown email."]);
                exit();
            }

            $q->close();
        }
    }
    $connection->close();
?>