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

$result = $db->query('SELECT company_id FROM users WHERE id ='.$_SESSION['id']);
$companyID = '';
foreach ($result as $row) {
    $companyID = $row['company_id'];
}

$result = $db->query('SELECT id,name FROM restaurant WHERE company_id ='.$companyID);
$restaurants = [];
foreach ($result as $row) {
    $restaurants[] = [
        'id'=> $row['id'],
        'name'=> $row['name']
    ];
}

$foods = [];
foreach ($restaurants as $index => $restaurant) {
    if (!isset($_GET["search"]) || $_GET["search"] == "") {
        $result = $db->query('SELECT id, restaurant_id, name, description, image_path, price, created_at, deleted_at FROM food WHERE restaurant_id ='.$restaurant['id']);
    }
    else {
        $searchedName = '%'.$_GET['search'].'%';
        $stmt = $db->query('SELECT id, restaurant_id, name, description, image_path, price, created_at, deleted_at FROM food WHERE restaurant_id ='.$restaurant['id'].' AND name LIKE :searchedName');
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
            'created_at'=> $row['created_at'],
            'deleted_at'=> $row['deleted_at']
        ];
    }
}


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $date = date('d/m/Y');
    $update = $db->prepare('UPDATE food SET deleted_at = :deleted_at WHERE id = :id');
    $update->bindParam(':deleted_at', $date, PDO::PARAM_STR);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
    $update->execute();
    header("Location: manageFood.php");
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Yemek Yönetim</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  
  <div class="navbarContainer">
  <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto; vertical-align: middle;">
    <h1>Yemek Yönetim</h1>
        <form method="GET" action="manageFood.php">
            <div>Yemek Ara: </div>
            <input type="text" name="search" placeholder="Yemek Ara" value="">
            <button type="submit">Ara</button>
        </form>
        <button class="button" onclick="window.location.href='addFood.php'">Yemek Ekle</button>
        <button class="button" onclick=goToCompanyPanel()>Geri Dön</button>
  <div class="list">
    <table>
      <thead>
        <tr>
            <th style="text-align: center">Resim</th>
            <th style="text-align: center">Restoran</th>
            <th style="text-align: center">İsim</th>
            <th style="text-align: center">Açıklama</th>
            <th style="text-align: center">Fiyat</th>
            <th style="text-align: center">Oluştuma Tarihi</th>
            <th style="text-align: center">Silinme Tarihi</th>
            <th style="text-align: center">İşlemler</th>
        </tr>
    </thead>
        
      <tbody id="list">
        <?php foreach ($foods as $index => $food): ?>
          <tr>
            <td><img src=<?php echo $food['image_path']; ?> alt="image" style="margin-left: 5%; width: 50px; height: auto;"></td>
            <td style="color:black"><?php foreach ($restaurants as $index => $restaurant){
                if($food['restaurant_id'] == $restaurant['id']){
                    echo $restaurant['name'];
                    }
                }?>
            </td>
            <td style="color:black"><?php echo $food['name']; ?></td>
            <td style="color:black"><?php echo $food['description']; ?></td>
            <td style="color:black"><?php echo $food['price']; ?></td>
            <td style="color:black"><?php echo $food['created_at']; ?></td>
            <td style="color:black"><?php echo $food['deleted_at']; ?></td>
            <td>
                <button class="button" onclick="window.location.href='manageFood.php?id=<?php echo $food['id']; ?>'">Sil</button>
                <button class="button" onclick="window.location.href='updateFood.php?id=<?php echo $food['id']; ?>'">Güncelle</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  </div>
  <script src="script.js"></script>
</body>
</html>
