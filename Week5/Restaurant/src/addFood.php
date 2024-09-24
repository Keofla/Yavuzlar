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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $restaurant_id = $_POST['restaurant'];
    $created_at = date('d/m/Y');

    
    $upload_dir = 'images/foods/';
    $uploaded_file = $upload_dir.str_replace(' ', '_', $_FILES['logo']['name']);
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploaded_file)) {
        $logo_path = $uploaded_file;
    }

    $update = $db->prepare('INSERT INTO food (restaurant_id, name, description, image_path, price, created_at) 
        VALUES(:restaurant_id, :name, :description, :image_path, :price, :created_at)');
    $update->bindParam(':name', $name, PDO::PARAM_STR);
    $update->bindParam(':description', $description, PDO::PARAM_STR);
    $update->bindParam(':image_path', $logo_path, PDO::PARAM_STR);
    $update->bindParam(':price', $price, PDO::PARAM_STR);
    $update->bindParam(':restaurant_id', $restaurant_id, PDO::PARAM_STR);
    $update->bindParam(':created_at', $created_at, PDO::PARAM_STR);
    $update->execute();
    header("Location: manageFood.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Şirket Düzenle</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="navbarContainer">
    <h2>Şirket Düzenle</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div>
            <div>Yemek Resmi: </div>
            <label for="logo">Upload Image</label>
            <input type="file" id="logo" name="logo" accept="image/png, image/jpeg"><br><br> 
        </div><br><br>
        <div class="formBox">
            <div>İsim: </div>
            <input type="text" id="name" name="name" placeholder="İsim" value="" required><br><br>
        </div><br><br>
        <div class="formBox">
            <div>Açıklama: </div>
            <input type="text" id="description" name="description" placeholder="Açıklama" value="" required>
        </div><br><br>
        <div class="formBox">
            <div>Fiyat: </div>
            <input type="text" id="price" name="price" placeholder="Fiyat" value="" required>
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
            <button name="updateCompany" type="submit">Yemeği Ekle</button>
        </div>
    </form>
    <button class="button" onclick=goToManageFoodPage()>Geri Dön</button>
  </div>

<script src="script.js"></script>
</body>

</html>