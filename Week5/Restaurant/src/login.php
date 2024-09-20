<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];

    $db = new PDO('sqlite:restaurant.db');
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwd, $user['passwd'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['deleted_at'] != null) {
          header("Location: login.php");
        }
        else {
            header("Location: index.php");
        }
    } 
    else {
        echo "<script>
            alert('Yanlış kullanıcı adı veya Parola');
          </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="navbarContainer">
        <form method="post" action="login.php">
            <label for="username">Kullanıcı Adı:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="passwd">Parola:</label>
            <input type="password" id="passwd" name="passwd" required>
            <br>
            <button type="submit" >Giriş Yap</button>
        </form>
        <button class=button id="goToLoginPageButton" onclick="goToRegisterPage()" >Kayıt Ol</button>
    </div>
    <script src="script.js"></script>
</body>
</html>
