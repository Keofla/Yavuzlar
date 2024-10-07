<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
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
if (!isset($_GET["search"]) || $_GET["search"] == "") {
    $result = $db->query('SELECT * FROM company');
}
else {
  $searchedName = '%'.$_GET['search'].'%';
    $stmt = $db->prepare('SELECT * FROM company WHERE name LIKE :searchedName');
    $stmt->bindParam(':searchedName', $searchedName, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt;
}
$companies = [];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $date = date('d/m/Y');
    $update = $db->prepare('UPDATE company SET deleted_at = :deleted_at WHERE id = :id');
    $update->bindParam(':deleted_at', $date, PDO::PARAM_STR);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
    $update->execute();
    header("Location: listCompany.php");
}
if (isset($_GET["filtre"])) {
    foreach ($result as $row) {
        if ($row["deleted_at"] != null) {
            $companies[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'logo_path' => $row['logo_path'],
                'deleted_at'=> $row['deleted_at']
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
          $companies[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'logo_path' => $row['logo_path'],
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
  <title>Şirket Yönetim Paneli</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
  <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
  <h1>Şirket Yönetim Paneli</h1>
  <div class="companyList">
    <table>
      <thead>
        <tr>
            <th style="text-align: center">Logo</th>
            <th style="text-align: center">İsim</th>
            <th style="text-align: center">Açıklama</th>
            <?php if (isset($_GET["filtre"])): ?>
            <th style="text-align: center">Silinme Tarihi</th>
            <?php endif; ?>
            <th style="text-align: center">İşlemler</th>
        </tr>
    </thead>
        <button class="button" onclick="window.location.href='listCompany.php?filtre=banned'">Silinmiş Şirketler</button>
        <button class="button" onclick=gotoAddCompany()>Şirket Ekle</button>
        <button class="button" onclick=gotoAdminPanel()>Geri Dön</button>
        <form method="GET" action="listCompany.php">
            <div>Şirket Adı Ara: </div>
            <input type="text" name="search" placeholder="Şirket Ara" value="">
            <button type="submit">Ara</button>
        </form>
      <tbody id="companyList">
        <?php foreach ($companies as $index => $company): ?>
          <tr>
            <td><img src="images\logos\<?php echo $company['logo_path']; ?>" alt="image" style="margin-left: 5%; width: 50px; height: auto;"></td>
            <td style="color:black" style="color:black"><?php echo $company['name']; ?></td>
            <td style="color:black"><?php echo $company['description']; ?></td>
            <?php if (isset($_GET["filtre"])): ?>
            <td style="color:black"><?php echo $company['deleted_at']; ?></td>
            <?php endif; ?>
            <td>
                <button class="button" onclick="window.location.href='listCompany.php?id=<?php echo $company['id']; ?>'">Sil</button>
                <button class="button" onclick="window.location.href='updateCompany.php?id=<?php echo $company['id']; ?>'">Güncelle</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  </div>
  <script src="script.js"></script>
</body>
</html>