<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

$db = new PDO('sqlite:restaurant.db');
if (!isset($_GET["search"]) || $_GET["search"] == "") {
    $result = $db->query('SELECT * FROM users');
}
else {
    $searchedName = '%'.$_GET['search'].'%';
    $stmt = $db->prepare('SELECT * FROM users WHERE username LIKE :searchedName');
    $stmt->bindParam(':searchedName', $searchedName, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt;
}
$users = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $date = date('d/m/Y');
    $update = $db->prepare('UPDATE users SET deleted_at = :deleted_at WHERE id = :id');
    $update->bindParam(':deleted_at', $date, PDO::PARAM_STR);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
    $update->execute();
    header("Location: listCustomer.php");
}
if (isset($_GET["filtre"])) {
    foreach ($result as $row) {
        if ($row["deleted_at"] != null) {
            $users[] = [
                'id' => $row['id'],
                'role' => $row['role'],
                'name' => $row['name'],
                'surname' => $row['surname'],
                'username' => $row['username'],
                'passwd' => $row['passwd'],
                'balance' => $row['balance'],
                'created_at' => $row['created_at'],
                'deleted_at'=> $row['deleted_at'],
            ];
        }
        else{
            continue;
        }
    }
}
else {
    foreach ($result as $row) {
        if ($row["deleted_at"] == null) {
            $users[] = [
                'id' => $row['id'],
                'role' => $row['role'],
                'name' => $row['name'],
                'surname' => $row['surname'],
                'username' => $row['username'],
                'passwd' => $row['passwd'],
                'balance' => $row['balance'],
                'created_at' => $row['created_at'],
            ];
        }
        else{
            continue;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sorular</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
  <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
  <h1>Müşteri Yönetim Paneli</h1>
  <div class="userList">
    <table>
      <thead>
        <tr>
            <th style="text-align: center">Rol</th>
            <th style="text-align: center">İsim</th>
            <th style="text-align: center">Soyisim</th>
            <th style="text-align: center">Kullanıcı Adı</th>
            <th style="text-align: center">Oluşturma Tarihi</th>
            <?php if (isset($_GET["filtre"])): ?>
            <th style="text-align: center">Silinme Tarihi</th>
            <?php endif; ?>
            <?php if (!isset($_GET["filtre"])): ?>
            <th style="text-align: center">İşlemler</th>
            <?php endif; ?>
        </tr>
    </thead>
        <button class="button" onclick="window.location.href='listCustomer.php?filtre=banned'">Silinmiş Kullanıcılar</button>
        <button class="button" onclick=gotoAdminPanel()>Geri Dön</button>
        <form method="GET" action="listCustomer.php">
            <div>Kullanıcı Adı Ara: </div>
            <input type="text" name="search" placeholder="Kullanıcı Ara" value="">
            <button type="submit">Ara</button>
        </form>
      <tbody id="userList">
        <?php foreach ($users as $index => $user): ?>
          <tr>
            <td style="color:black"><?php echo $user['role']; ?></td>
            <td style="color:black"><?php echo $user['name']; ?></td>
            <td style="color:black"><?php echo $user['surname']; ?></td>
            <td style="color:black"><?php echo $user['username']; ?></td>
            <td style="color:black"><?php echo $user['created_at']; ?></td>
            <?php if (isset($_GET["filtre"])): ?>
            <td style="color:black"><?php echo $user['deleted_at']; ?></td>
            <?php endif; ?>
            <?php if (!isset($_GET["filtre"])): ?>
            <td>
                <button class="button" onclick="window.location.href='listCustomer.php?id=<?php echo $user['id']; ?>'">Sil</button>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  </div>
  <script src="script.js"></script>
</body>
</html>
