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
        'id' => $row['id'],
        'username' => $row['username'],
        'balance' => $row['balance']
    ];
}

$result = $db->query('SELECT id,name,imape_path FROM restaurant');
$restaurants = [];
foreach ($result as $row) {
    $restaurants[] = [
        'id'=> $row['id'],
        'name'=> $row['name'],
        'imape_path' => $row['imape_path']
    ];
}

$foods = [];
if (!isset($_GET["search"]) || $_GET["search"] == "") {
  if (!isset($_GET["order"]) || $_GET["order"] == ""){
    $result = $db->query('SELECT id, restaurant_id, name, description, image_path, price, created_at, deleted_at FROM food ');
  }
  else if ($_GET["order"] == "ASC"){
    $result = $db->query('SELECT id, restaurant_id, name, description, image_path, price, created_at, deleted_at FROM food ORDER BY price ASC');
  }
  else {
    $result = $db->query('SELECT id, restaurant_id, name, description, image_path, price, created_at, deleted_at FROM food ORDER BY price DESC');
  }
}

else {
    $searchedName = '%'.$_GET['search'].'%';
    $stmt = $db->query('SELECT id, restaurant_id, name, description, image_path, price, created_at, deleted_at FROM food WHERE AND name LIKE :searchedName');
    $stmt->bindParam(':searchedName', $searchedName, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt;
}

foreach ($result as $row) {
    $foods[] = [
        'id'=> $row['id'],
        'restaurant_id'=> $row['restaurant_id'],
        'name'=> $row['name'],
        'description'=> $row['description'],
        'image_path'=> $row['image_path'],
        'price'=> $row['price'],
    ];
}

$result = $db->query('SELECT * FROM coupon');
$coupons = [];
foreach ($result as $row) {
    $coupons[] = [
        'restaurant_id'=> $row['restaurant_id'],
        'name'=> $row['name'],
        'discount' => $row['discount']
    ];
}

$result = $db->query('SELECT restaurant.id, AVG(comments.score) AS averageScore FROM restaurant LEFT JOIN comments  ON restaurant.id = comments.restaurant_id GROUP BY restaurant.id');
$avgScores = [];
foreach ($result as $row) {
  $avgScores[] = [
      'id'=> $row['id'],
      'averageScore'=> $row['averageScore']
  ];
}

if (isset($_GET["id"])) {
  $user_id = $_SESSION['id'];
  $food_id = $_GET["id"];
  $note = '';
  $quantity = '1';
  $created_at = date('d/m/Y');

  $update = $db->prepare("INSERT INTO basket (user_id, food_id, note, quantity, created_at) VALUES (:user_id, :food_id, :note, :quantity, :created_at)");
  $update->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $update->bindParam(':food_id', $food_id, PDO::PARAM_INT);
  $update->bindParam(':note', $note, PDO::PARAM_STR);
  $update->bindParam(':quantity', $quantity, PDO::PARAM_INT);
  $update->bindParam(':created_at', $created_at, PDO::PARAM_STR);
  $update->execute();
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anasayfa</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
    <div class="header">
      <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto; vertical-align: middle;">

      <h1>Yavuzlar Restoran Platformu</h1>

      <div class="username">Kullanıcı Adı: <?php echo $users['username'];?></div>
      <div class="balance">Bakiye: <?php echo $users['balance'];?></div>

      <div class="coupons">KUPONLAR: </div>
      <?php foreach ($coupons as $index => $coupon):?>
      <div class="coupons"> Kupon Kodu: <?php echo $coupon['name'];?>  İndirim: <?php echo $coupon['discount'];?> TL</div>
      <?php endforeach; ?>

      <button class="button" id="profileButton" onclick="goToProfile()">Kullanıcı Panel</button>

      <?php if($_SESSION['role'] == 'company'):?>
      <button class="button" id="restaurantPanelButton" onclick="gotoRestaurantPanel()">Restoran Paneli</button>
      <?php endif;?>

      <?php if($_SESSION['role'] == 'admin'):?>
      <button class="button" id="adminPanelButton" onclick="gotoAdminPanel()">Admin Paneli</button>
      <?php endif;?>

      <button class="button" id="basketButton" onclick="gotoBasket()">Sepete Git</button>
      <button class="button" id="basketButton" onclick="gotoOrders()">Sipariş Geçmişi</button>
      <button class="button" id="logoutButton" onclick="logout()">Çıkış yap</button>
    </div>
  </div>
  <div class="list">
  <form method="GET" action="index.php">
            <div>Yemek Ara: </div><input type="text" name="search" placeholder="Yemek Ara" value="">
            <button type="submit">Ara</button>
            <button type="submit" name="order" value="DSC">Azalan Fiyat</button>
            <button type="submit" name="order" value="ASC">Artan Fiyat</button>
        </form>
  <?php foreach ($restaurants as $index => $restaurant):?>
    <div><img src=<?php echo $restaurant['imape_path']; ?> alt="image" style="margin-left: 5%; width: 50px; height: auto;">
          <?php echo $restaurant['name'];?>
          Puan: <?php echo $avgScores[$index]['averageScore'];?>
          <button class="button" onclick="window.location.href='comments.php?id=<?php echo $restaurant['id'];?>'">Yorumlar</button>
  </div>
    <table>
      <thead>
        <tr>
            <th>Resim</th>
            <th>İsim</th>
            <th>Açıklama</th>
            <th>Fiyat</th>
            <th>İşlemler</th>
        </tr>
    </thead>
        
      
      <tbody id="list">
        <?php foreach ($foods as $index => $food): ?>
          <?php if ($food['restaurant_id'] == $restaurant['id']):?>
          <tr>
            <td><img src=<?php echo $food['image_path']; ?> alt="image" style="margin-left: 5%; width: 50px; height: auto;"></td>
            <td><?php echo $food['name']; ?></td>
            <td><?php echo $food['description']; ?></td>
            <td><?php echo $food['price']; ?></td>
            <td>
              <button class="button" onclick="window.location.href='index.php?id=<?php echo $food['id'];?>'">Sepete Ekle</button>
            </td>
          </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table><br></br>
    <?php endforeach; ?>
  </div>
  <script src="script.js"></script>
</body>
</html>