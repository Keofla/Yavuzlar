<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'company') {
    header("Location: index.php");
    exit();
}

$db_host = 'db';
$db_port = '3306';
$db_user = 'user';
$db_pass = 'user';
$db_name = 'restaurantapp';

$dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name";
$db = new PDO($dsn, $db_user, $db_pass);
$result = $db->query('SELECT * FROM users WHERE id='.$_SESSION['id']);
$users = [];
foreach ($result as $row) {
    $users = [
        'id' => $row['id'],
        'company_id'=> $row['company_id'],
        'role' => $row['role'],
        'name' => $row['name'],
        'surname' => $row['surname'],
        'username' => $row['username'],
        'passwd' => $row['passwd'],
        'balance' => $row['balance'],
        'created_at' => $row['created_at'],
        'deleted_at' => $row['deleted_at']
    ];
}

?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restoran Paneli</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
  <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
    <h1>Restoran Paneli</h1>
    <div class="header">
      
      <button class="button" id="manageFoodButton" onclick="goToManageFoodPage()">Yemek Yönetimi</button>
      <button class="button" id="manageOrderButton" onclick="goToManageOrderPage()">Sipariş Yönetimi</button>
      <button class="button" onclick="gotoMainMenu()">Geri Dön</button>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>