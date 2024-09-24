<?php
session_start();

$db_host = 'db';
$db_port = '3306';
$db_user = 'user';
$db_pass = 'user';
$db_name = 'restaurantapp';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $passwd = $_POST['passwd'];

    $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name";
    $db = new PDO($dsn, $db_user, $db_pass);
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($passwd, $user['passwd'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
    } else {
        echo "<script>alert('Yanlış kullanıcı adı veya Parola');</script>";
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
