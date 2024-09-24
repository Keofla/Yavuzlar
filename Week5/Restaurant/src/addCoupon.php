<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: homePage.php");
    exit();
}
$db_host = 'db';
$db_port = '3306';
$db_user = 'user';
$db_pass = 'user';
$db_name = 'restaurantapp';

$dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name";
$db = new PDO($dsn, $db_user, $db_pass);
$result = $db->query('SELECT * FROM coupon');
$coupons = [];
foreach ($result as $row) {
    $coupons[] = [
        'id'=> $row['id'],
        'restaurant_id'=> $row['restaurant_id'],
        'name' => $row['name'],
        'discount' => $row['discount'],
        'created_at'=> $row['created_at']
    ];
}

$result = $db->query('SELECT id, name FROM restaurant');
$restaurants = [];
foreach ($result as $row) {
    $restaurants[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $discount = $_POST['discount'];
    $restaurant_id = $_POST['restaurant'];
    $created_at = date('d/m/Y');
    
    $update = $db->prepare('INSERT INTO coupon (restaurant_id, name, discount, created_at) 
        VALUES(:restaurant_id, :name, :discount, :created_at)');
    $update->bindParam(':restaurant_id', $restaurant_id, type: PDO::PARAM_STR);
    $update->bindParam(':name', $name, PDO::PARAM_STR);
    $update->bindParam(':discount', $discount, PDO::PARAM_STR);
    $update->bindParam(':created_at', $created_at, PDO::PARAM_STR);
    $update->execute();
    header("Location: addCoupon.php");
}

if (isset($_GET['id'])){
    $id = $_GET['id'];
    $delete = $db->prepare('DELETE FROM coupon WHERE id ='.$id);
    $delete->execute();
    header("Location: addCoupon.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kupon Ekle</title>

  <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navbarContainer">
    <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
    <h1>Kupon Ekle</h1>
      <form action="" method="POST">
      <div class="formBox">
        <input type="text" id="name" name="name" placeholder="Kupon Kodu" required><br><br>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="discount" name="discount" placeholder="İndirim Miktarı" required>
      </div><br><br>
      <div class="formBox" >
        <label for="restaurant">Restoran:</label>
        <select name="restaurant" id="restaurant" required>
          <option value="" selected disabled>Restoran Seçiniz</option>
          <?php foreach ($restaurants as $index => $restaurant):?>
          <option value=<?php echo $restaurant['id']?>><?php echo $restaurant['name']?></option>
          <?php endforeach;?>
        </select><br><br>
      </div>
      <div class="formBox">
        <button name="addQuestion" type="submit">Kupon Ekle</button>
      </div>
    </form>
      <button class="button" onclick="gotoAdminPanel()">Geri Dön</button>
    </div>

    <div class="navbarContainer">
    <table>
      <thead>
        <tr>
            <th style="text-align: center">Restoran</th>
            <th style="text-align: center">İndirim Kodu</th>
            <th style="text-align: center">İndirim Oranı</th>
            <th style="text-align: center">Oluşturma Tarihi</th>
            <th style="text-align: center">İşlemler</th>
        </tr>
    </thead>
      <tbody id="companyList">
        <?php foreach ($coupons as $index => $coupon): ?>
          <tr>
            <td style="color:black"><?php echo $restaurants[$coupon['restaurant_id']-1]['name']; ?></td>
            <td style="color:black"><?php echo $coupon['name']; ?></td>
            <td style="color:black"><?php echo $coupon['discount']; ?></td>
            <td style="color:black"><?php echo $coupon['created_at']; ?></td>
            <td>
                <button class="button" onclick="window.location.href='addCoupon.php?id=<?php echo $coupon['id']; ?>'">Sil</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <script src="script.js"></script>
</body>

</html>