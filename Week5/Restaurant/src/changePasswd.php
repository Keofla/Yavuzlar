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
        'passwd' => $row['passwd'],
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $passwd = $_POST['passwd'];
    $newPasswd = $_POST['newPasswd'];
    $newPasswdCheck = $_POST['newPasswdCheck'];

    if ($passwd != $users['passwd']) {
        echo "<script>
            alert('Şifreni Yanlış Girdin');
            window.location.href = 'changePasswd.php';
          </script>";
    }

    else if ($newPasswd != $newPasswdCheck) {
        echo "<script>
            alert('Yeni Şifre Birbiri İle Uyuşmadı');
            window.location.href = 'changePasswd.php';
          </script>";
    }

    else{
        $newPasswd = password_hash($newPasswd, PASSWORD_ARGON2ID);
        $update = $db->prepare('UPDATE users SET passwd = :passwd WHERE id = :id');
        $update->bindParam(':passwd', $newPasswd, PDO::PARAM_STR);
        $update->bindParam(':id', $_SESSION['id'], PDO::PARAM_INT);
        $update->execute();
        header("Location: profilePage.php");
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Şifre Değiştir</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
  <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
      <h1>Şifre Değiştir</h1>
      <button class="button" onclick="gotoMainMenu()">Geri Dön</button>
    <form action="" method="POST" >
        <div class="formBox">
            <input type="password" id="passwd" name="passwd" placeholder="Eski Şifre" value="" required><br><br>
        </div><br><br>
        <div class="formBox">
            <input type="password" id="newPasswd" name="newPasswd" placeholder="Yeni Şifre" value="" required>
        </div><br><br>
        <div class="formBox">
            <input type="password" id="newPasswdCheck" name="newPasswdCheck" placeholder="Yeni Şifre Tekrar" value="" required>
        </div><br><br>
        <div class="formBox">
            <button name="updatePasswd" type="submit">Şifre Güncelle</button>
        </div>
    </form>
    </div>
</body>
</html>