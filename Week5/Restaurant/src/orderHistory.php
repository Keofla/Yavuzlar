<?php
session_start();
if (!isset($_SESSION['id'])) {
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
$result = $db->query("SELECT * FROM `order` WHERE user_id=".$_SESSION['id']);
$orders = [];
foreach ($result as $row) {
    $orders[] = [
        'id' => $row['id'],
        'order_status' => $row['order_status'],
        'total_price' => $row['total_price'],
        'created_at' => $row['created_at'],
        'note' => $row['note']
    ];
}

$orderItems = [];
foreach ($orders as $index => $order) {  
    $result = $db->query("SELECT * FROM `order_items` WHERE order_id=".$order['id']." ORDER BY order_id");
    foreach ($result as $row) {
        $orderItems[] = [
            'id' => $row['id'],
            'food_id' => $row['food_id'],
            'quantity' => $row['quantity']
        ];
    }
}

$foods = [];
foreach ($orderItems as $index => $orderItem) {  
    $result = $db->query("SELECT * FROM `food` WHERE id=".$orderItem['food_id']);
    foreach ($result as $row) {
        $foods[] = [
            'name' => $row['name']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sipariş Geçmişi</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
      <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
      <h1>Sipariş Geçmişi</h1>
      <button class="button" onclick="gotoMainMenu()">Geri Dön</button>
  </div>

  <div class="navbarContainer">
    <?php foreach ($orders as $index => $order):?>
        <div class="orderCard">
            <div class="orderItem">Yemek: <?php echo $foods[$index]['name'];?></div>
            <div class="orderItem">Miktar: <?php echo $orderItems[$index]['quantity'];?></div>
            <div class="orderItem">Sipraiş Durumu: <?php echo $order['order_status'];?></div>
            <div class="orderItem">Toplam Tutar: <?php echo $order['total_price'];?></div>
            <div class="orderItem">Not: <?php echo $order['note'];?></div>
            <div class="orderItem">Sipariş Tarihi: <?php echo $order['created_at'];?></div><br></br>
        </div>
    <?php endforeach; ?>
    </div>
    
  <script src="script.js"></script>
</body>
</html>