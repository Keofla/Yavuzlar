<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];
    $role = $_POST['role'];

    $db = new PDO('sqlite:QuestAppDB.db');
    $stmt = $db->prepare("INSERT INTO user (username, passwd, role) VALUES (:username, :passwd, :role)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':passwd', $passwd);
    $stmt->bindParam(':role', $role);
    if ($stmt->execute()) {
        echo "<script>
            alert('Kayıt tamamlandı');
            window.location.href = 'login.php';
          </script>";
    } else {
        echo "Kayıt Başarısız.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kayıt Ol</title>

  <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbarContainer">
    <form method="post" action="register.php">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="passwd">Parola:</label>
        <input type="password" id="passwd" name="passwd" required>
        <br>
        <div style="margin-top: 10px;" class="formBox">
          <label for="role">Rol:</label>
          <select name="role" id="role" >
            <option value="" selected disabled>Rol Seçiniz</option>
            <option value="admin">Admin</option>
            <option value="student">Öğrenci</option>
          </select><br><br>
        </div>
        <div class="formBox"></div>
        <button type="submit" style="height:50px">Kayıt Ol</button>
    </form>
    <button class=button id="goToLoginPageButton" onclick="goToLoginPage()" style="height: 50px;">Geri Dön</button>
    </div>
    <script src="script.js"></script>
</body>
</html>
