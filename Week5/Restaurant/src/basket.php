<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new PDO('sqlite:restaurant.db');
$result = $db->query('SELECT * FROM users WHERE id='.$_SESSION['id']);
$users = [];
foreach ($result as $row) {
    $users = [
        'username' => $row['username'],
        'balance' => $row['balance']
    ];
}

$result = $db->query('SELECT * FROM basket WHERE user_id='.$_SESSION['id']);
$basketItems = [];
foreach ($result as $row) {
  if ($row['deleted_at'] == null) {
    $basketItems[] = [
        'id'=> $row['id'],
        'food_id' => $row['food_id'],
        'note' => $row['note'],
        'quantity'=> $row['quantity'],
    ];
  }
  else{
    continue;
  }
}

$result = $db->query('SELECT * FROM food');
$foods = [];
foreach ($result as $row) {
    $foods[] = [
        'id' => $row['id'],
        'restaurant_id'=> $row['restaurant_id'],
        'name' => $row['name'],
        'price'=> $row['price'],
    ];
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = $_POST['id'];
  $note = $_POST['note'];
  $quantity = $_POST['quantity'];
  $foodPrice = $_POST['foodPrice'];

  $result = $db->query('SELECT * FROM coupon WHERE restaurant_id = '.$_POST['restaurantID']);
  $coupon = [];
  foreach ($result as $row) {
      $coupon = [
          'name' => $row['name'],
          'discount'=> $row['discount'],
      ];
  }

  if ($coupon != []) {
    if ($_POST['coupon'] == $coupon['name']){
      $foodPrice = $foodPrice - $coupon['discount'];
    }
    else{
      echo "<script>
            alert('Geçersiz Kupon');
            window.location.href = 'basket.php';
          </script>";
    }
  }

  $total_price = $_POST['quantity'] * $foodPrice;
  $created_at = date('d/m/Y');
  $leftBalance = $users['balance'] - $total_price;

  if ($users['balance'] >= $total_price) {
    $update = $db->prepare("UPDATE users SET balance = :balance WHERE id = :id");
    $update->bindParam(':balance', $leftBalance, PDO::PARAM_INT);
    $update->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $update->execute();

    $update = $db->prepare("UPDATE basket SET deleted_at = :deleted_at, note = :note, quantity = :quantity WHERE id = :id");
    $update->bindParam(':deleted_at', $created_at, PDO::PARAM_STR);
    $update->bindParam(':note', $note, PDO::PARAM_STR);
    $update->bindParam(':quantity', $created_at, PDO::PARAM_INT);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
    $update->execute();


    $update = $db->prepare("INSERT INTO 'order' (user_id, total_price, note, created_at) VALUES (:user_id, :total_price, :note, :created_at)");
    $update->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_STR);
    $update->bindParam(':total_price', $total_price, PDO::PARAM_INT);
    $update->bindParam(':note', $note, PDO::PARAM_STR);
    $update->bindParam(':created_at', $created_at, PDO::PARAM_STR);
    $update->execute();

    $result = $db->query("SELECT id FROM 'order' ORDER BY ID DESC LIMIT 1");
    $orderID = [];
    foreach ($result as $row) {
      $orderID[] = [
          'id' => $row['id']
      ];
    }

    $update = $db->prepare("INSERT INTO 'order_items' (food_id, order_id, quantity, price) VALUES (:food_id, :order_id, :quantity, :price)");
    $update->bindParam(':food_id', $_POST['foodID'], PDO::PARAM_INT);
    $update->bindParam(':order_id', $orderID[0]['id'], PDO::PARAM_INT);
    $update->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $update->bindParam(':price', $_POST['foodPrice'], PDO::PARAM_INT);
    $update->execute();
    header("Location: basket.php");
  }

  else{
    echo "<script>
            alert('Yetersiz Bakiye');
            window.location.href = 'basket.php';
          </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sepet</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
  <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
      <h1>Sepetim</h1>
      <div class="balance">Bakiye: <?php echo $users['balance'];?></div>
  <?php foreach ($basketItems as $index => $basketItem):?>
    <?php foreach ($foods as $index => $food):?>
      <?php if($food['id'] == $basketItem['food_id']):?>
        <form action="" method="POST">
        <div><?php echo $food['name'];?></div>
        <div class="formBox">
            <input type="text" id="quantity" name="quantity" placeholder="Sayı" value="<?php echo $basketItem['quantity']; ?>" required><br><br>
        </div>
          <div>
            <input type="text" id="note" name="note" placeholder="Not" value="<?php echo $basketItem['note']; ?>" >
        </div><br><br>
        <div>
            <input type="text" id="coupon" name="coupon" placeholder="Kupon" value="" >
        </div><br><br>
        <div><input type="hidden" id="id" name="id" placeholder="id" value="<?php echo $basketItem['id']; ?>"></div>
        <div><input type="hidden" id="foodID" name="foodID" placeholder="foodID" value="<?php echo $basketItem['food_id']; ?>"></div>
        <div><input type="hidden" id="foodPrice" name="foodPrice" placeholder="foodPrice" value="<?php echo $food['price']; ?>"></div>
        <div><input type="hidden" id="restaurantID" name="restaurantID" placeholder="restaurantID" value="<?php echo $food['restaurant_id']; ?>"></div>
        <div class="formBox">
            <button name="updateOrder" type="submit">Sipariş Tamamla</button>
        </div>
    </form>
      <?php endif?>  
      <?php endforeach?>    
  <?php endforeach?>
  <button class="button" onclick="gotoMainMenu()">Geri Dön</button>
  </div>

<script src="script.js"></script>
</body>

</html>