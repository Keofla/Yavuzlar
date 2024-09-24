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

$id = '';
$foods = [];
if(isset($_GET['id'])) {
  $id = $_GET['id'];
  $result = $db->query('SELECT * FROM food WHERE id = '.$id);
  foreach ($result as $row) {
    $foods[] = [
        'restaurant_id'=> $row['restaurant_id'],
        'name'=> $row['name'],
        'description'=> $row['description'],
        'image_path'=> $row['image_path'],
        'price'=> $row['price'],
    ];
  }
} else {
    header("Location: companyPanel.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $restaurant_id = $_POST['restaurant'];
    
    $upload_dir = 'images/foods/';
    if ($_FILES['logo']['name'] == null || $_FILES['logo']['name'] == ''){
        $logo_path = $foods[0]['image_path'];
    }
    else{
        $uploaded_file = $upload_dir.str_replace(' ', '_', $_FILES['logo']['name']);
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploaded_file)) {
        $logo_path = $uploaded_file;
    }
    }
    

    $update = $db->prepare('UPDATE food SET name = :name, description = :description, price = :price, image_path = :image_path, restaurant_id = :restaurant_id WHERE id = :id');
    $update->bindParam(':name', $name, PDO::PARAM_STR);
    $update->bindParam(':description', $description, PDO::PARAM_STR);
    $update->bindParam(':image_path', $logo_path, PDO::PARAM_STR);
    $update->bindParam(':price', $price, PDO::PARAM_STR);
    $update->bindParam(':restaurant_id', $restaurant_id, PDO::PARAM_STR);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
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
        <div class="formBox">
            <img src="<?php echo $foods[0]['image_path']; ?>" alt="image" style="margin-left: 5%; width: 200px; height: auto;">
        </div>
        <div>
            <label for="logo">Upload Image</label>
            <input type="file" id="logo" name="logo" accept="image/png, image/jpeg"><br><br> 
        </div><br><br>
        <div class="formBox">
            <div>İsim: </div>
            <input type="text" id="name" name="name" placeholder="İsim" value="<?php echo $foods[0]['name']; ?>" required><br><br>
        </div><br><br>
        <div class="formBox">
            <div>Açıklama: </div>
            <input type="text" id="description" name="description" placeholder="Açıklama" value="<?php echo $foods[0]['description']; ?>" required>
        </div><br><br>
        <div class="formBox">
            <div>Fiyat: </div>
            <input type="text" id="price" name="price" placeholder="Fiyat" value="<?php echo $foods[0]['price']; ?>" required>
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
            <button name="updateCompany" type="submit">Yemeği Güncelle</button>
        </div>
    </form>
    <button class="button" onclick="goToManageFoodPage()">Geri Dön</button>
  </div>

<script src="script.js"></script>
</body>

</html>