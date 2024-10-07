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
$result = $db->query('SELECT * FROM users WHERE id='.$_SESSION['id']);
$users = [];
foreach ($result as $row) {
    $users = [
        'id' => $row['id'],
        'name' => $row['name'],
        'surname' => $row['surname'],
        'username' => $row['username'],
        'passwd' => $row['passwd'],
        'balance' => $row['balance'],
        'pfp_path'=> $row['pfp_path'],
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];

    
    $upload_dir = 'images/profilePic/';
    $uploaded_file = $upload_dir.str_replace(' ', '_', $_FILES['logo']['name']);
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploaded_file)) {
        $pfp_path = $uploaded_file;
    }

    $update = $db->prepare('UPDATE users SET name = :name, surname = :surname, pfp_path = :pfp_path, username = :username WHERE id = :id');
    $update->bindParam(':name', $name, PDO::PARAM_STR);
    $update->bindParam(':surname', $surname, PDO::PARAM_STR);
    $update->bindParam(':pfp_path', $pfp_path, PDO::PARAM_STR);
    $update->bindParam(':username', $username, PDO::PARAM_STR);
    $update->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
    $update->execute();
    header("Location: profilePage.php");
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
      <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
      <h1>Kullanıcı Paneli</h1>
      <button class="button" onclick="gotoMainMenu()">Geri Dön</button>
      <div class="balance">Bakiye: <?php echo $users['balance'];?> <button class="button" id="addBalanceButton" onclick="gotoAddBalance()">Bakiye Yükle</button><br><br>
    </div>
    <button class="button" id="gotoOrdersButton" onclick="gotoOrders()">Siparişler</button><br><br>
  </div>

  <div class="navbarContainer">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="formBox">
            <img src="images\profilePic\<?php echo $users['pfp_path']; ?>" alt="image" style="margin-left: 5%; width: 200px; height: auto;">
        </div>
        <div>
            <label for="logo">Resim Yükle</label>
            <input type="file" id="logo" name="logo" accept="image/png, image/jpeg"><br><br> 
        </div><br><br>
        <div class="formBox">
            <input type="text" id="name" name="name" placeholder="İsim" value="<?php echo $users['name']; ?>" required><br><br>
        </div><br><br>
        <div class="formBox">
            <input type="text" id="surname" name="surname" placeholder="Soyisim" value="<?php echo $users['surname']; ?>" required>
        </div><br><br>
        <div class="formBox">
            <input type="text" id="username" name="username" placeholder="Kullanıcı Adı" value="<?php echo $users['username']; ?>" required>
        </div><br><br>
        <div class="formBox">
            <button name="updateCompany" type="submit">Bilgileri Güncelle</button>
        </div>
    </form>
    </div>

    <div class="navbarContainer"><button class="button" id="changePasswdButton" onclick="changePasswd()">Şifre Değiştir</button></div>
    
  <script src="script.js"></script>
</body>
</html>