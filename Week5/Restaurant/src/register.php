<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $passwd = password_hash($_POST['passwd'], PASSWORD_ARGON2ID);
    $role = 'user';
    $created_at = date('d/m/Y');

    $db = new PDO('sqlite:restaurant.db');
    $stmt = $db->prepare("INSERT INTO users (name, surname, username, passwd, role, created_at) VALUES (:name, :surname, :username, :passwd, :role, :created_at)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':surname', $surname);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':passwd', $passwd);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':created_at', $created_at);
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
    <form method="post" action="">
        <label for="name">İsim:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="surname">Soyisim:</label>
        <input type="text" id="surname" name="surname" required>
        <br>
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="passwd">Parola:</label>
        <input type="password" id="passwd" name="passwd" required>
        <br>
        <button type="submit" >Kayıt Ol</button>
    </form>
    <button class=button id="goToLoginPageButton" onclick="goToLoginPage()">Geri Dön</button>
    </div>
    <script src="script.js"></script>
</body>
</html>
