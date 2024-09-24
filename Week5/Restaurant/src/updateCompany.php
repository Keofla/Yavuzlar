<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$id = '';
$companies = [];
$db_host = 'db';
$db_port = '3306';
$db_user = 'user';
$db_pass = 'user';
$db_name = 'restaurantapp';

$dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name";
$db = new PDO($dsn, $db_user, $db_pass);
if(isset($_GET['id'])) {
  $id = $_GET['id'];
  $result = $db->query('SELECT * FROM company WHERE id = '.$id);
  foreach ($result as $row) {
      $companies[] = [
          'id' => $row['id'],
          'name' => $row['name'],
          'description' => $row['description'],
          'logo_path' => $row['logo_path'],
          'deleted_at' => $row['deleted_at']
      ];
  }
} else {
    header("Location: adminPanel.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];

    
    $upload_dir = 'images/logos/';
    $uploaded_file = $upload_dir.str_replace(' ', '_', $_FILES['logo']['name']);
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploaded_file)) {
        $logo_path = $uploaded_file;
    }

    $update = $db->prepare('UPDATE company SET name = :name, description = :description, logo_path = :logo_path WHERE id = :id');
    $update->bindParam(':name', $name, PDO::PARAM_STR);
    $update->bindParam(':description', $description, PDO::PARAM_STR);
    $update->bindParam(':logo_path', $logo_path, PDO::PARAM_STR);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
    $update->execute();
    header("Location: listCompany.php");
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
            <img src="<?php echo $companies[0]['logo_path']; ?>" alt="image" style="margin-left: 5%; width: 200px; height: auto;">
        </div>
        <div>
            <label for="logo">Upload Image</label>
            <input type="file" id="logo" name="logo" accept="image/png, image/jpeg"><br><br> 
        </div><br><br>
        <div class="formBox">
            <input type="text" id="name" name="name" placeholder="İsim" value="<?php echo $companies[0]['name']; ?>" required><br><br>
        </div><br><br>
        <div class="formBox">
            <input type="text" id="description" name="description" placeholder="Açıklama" value="<?php echo $companies[0]['description']; ?>" required>
        </div><br><br>
        <div class="formBox">
            <button name="updateCompany" type="submit">Şirket Güncelle</button>
        </div>
    </form>
    <button id="listCompanyButton" onclick="goToListCompanyPage()">Geri Dön</button>
  </div>

<script src="script.js"></script>
</body>

</html>