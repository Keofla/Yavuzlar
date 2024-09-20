<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$db = new PDO('sqlite:restaurant.db');
$result = $db->query('SELECT * FROM users WHERE id='.$_SESSION['id']);
$users = [];
foreach ($result as $row) {
    $users = [
        'id' => $row['id'],
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
  <title>Admin Paneli</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
    <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
    <h1>Admin Paneli</h1>
    <div class="header">
      
      <button class="button" id="listCustomerButton" onclick="goToListCustomerPage()">Müşteri Listele</button>
      <button class="button" id="listCompanyButton" onclick="goToListCompanyPage()">Şirket Listele</button>
      <button class="button" id="AddCouponButton" onclick="goToAddCouponPage()">Kupon Yönetim</button>
      <button class="button" onclick="gotoMainMenu()">Geri Dön</button>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>