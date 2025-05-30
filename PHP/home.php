<?php
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.html");
  exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home | Dashboard</title>
  <link rel="stylesheet" href="../CSS/home.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
  <div class="home-container">
    <div class="content-box">
      <h1>Welcome</h1>
      <div class="user-info">
        <p><strong>Hi,</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
        <p><strong>You have logged in:</strong> <?= $user['login_count'] ?> times</p>
        <p><strong>Last login date:</strong> <?= date("F d, Y", strtotime($user['last_login'])) ?></p>
      </div>
      <div class="download-section">
        <a href="../company_confidential_file.txt" download class="btn">
          <i class='bx bxs-download'></i> Download Confidential File
        </a>
      </div>
    </div>
  </div>
</body>
</html>