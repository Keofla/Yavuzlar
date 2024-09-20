<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$companies = [];
$db = new PDO('sqlite:restaurant.db');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];

    
    $upload_dir = 'images/logos/';
    $uploaded_file = $upload_dir.str_replace(' ', '_', $_FILES['logo']['name']);
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploaded_file)) {
        $logo_path = $uploaded_file;
    }

    $update = $db->prepare('INSERT INTO company (name, description, logo_path) 
        VALUES(:name, :description, :logo_path)');
    $update->bindParam(':name', $name, PDO::PARAM_STR);
    $update->bindParam(':description', $description, PDO::PARAM_STR);
    $update->bindParam(':logo_path', $logo_path, PDO::PARAM_STR);
    $update->execute();
    header("Location: listCompany.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Şirket Ekle</title>

  <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navbarContainer">
    <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
    <h1>Şirket Ekle</h1>
      <form action="" method="POST" enctype="multipart/form-data">
      <div>
            <label for="logo">Upload Image</label>
            <input type="file" id="logo" name="logo" accept="image/png, image/jpeg"><br><br> 
        </div><br><br>
      <div class="formBox">
        <input type="text" id="name" name="name" placeholder="Şirket İsmi" required><br><br>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="description" name="description" placeholder="Şirket Açıklaması" required>
      </div><br><br>
      <div class="formBox">
        <button name="addCompany" type="submit">Şirket Ekle</button>
      </div>
    </form>
    <button class="button" onclick="goToListCompanyPage()">Geri Dön</button>
    </div>

  <script src="script.js"></script>
</body>

</html>