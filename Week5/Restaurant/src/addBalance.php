<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$db = new PDO('sqlite:restaurant.db');
$result = $db->query('SELECT * FROM users WHERE id='.$_SESSION['id']);
$users = [];
foreach ($result as $row) {
    $users = [
        'id' => $row['id'],
        'balance' => $row['balance'],
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $balance = $users['balance'] + $_POST['balance'];


    $update = $db->prepare('UPDATE users SET balance = :balance WHERE id = :id');
    $update->bindParam(':balance', $balance, PDO::PARAM_INT);
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
    <div class="header">
      <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
      <h1>Şirket Paneli</h1>
      <button class="button" id="addBalanceButton" onclick="goToProfile()">Geri Dön</button>
      <div>balance: <?php echo $users['balance'];?></div>
      <form action="" method="POST">
        <div class="formBox">
            <input type="text" id="balance" name="balance" placeholder="Bakiye" value="" required>
        </div><br><br>
        <div class="formBox">
            <button name="addBalance" type="submit">Bakiye Ekle</button>
        </div>
    </form>
  </div>
  <script src="script.js"></script>
</body>
</html>