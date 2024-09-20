<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'company') {
    header("Location: index.php");
    exit();
}

$db = new PDO('sqlite:restaurant.db');

$result = $db->query('SELECT company_id FROM users WHERE id ='.$_SESSION['id']);
$companyID = '';
foreach ($result as $row) {
    $companyID = $row['company_id'];
}

$result = $db->query('SELECT * FROM restaurant WHERE company_id ='.$companyID);
$restaurants = [];
foreach ($result as $row) {
    $restaurants[] = ['id' => $row['id'],
        'name'=> $row['name']];
}

$foods = [];
foreach ($restaurants as $restaurant) {
    $result = $db->query('SELECT * FROM food WHERE restaurant_id ='.$restaurant['id']);
    foreach ($result as $row) {
        $foods[] = ['id' => $row['id'],
            'name'=> $row['name']];
    }
}

$orderItems = [];

foreach ($foods as $food) {
    $result = $db->query('SELECT * FROM order_items WHERE food_id = '.$food['id']);
    foreach ($result as $row) {
        $orderItems[] = [
            'id' => $row['id'],
            'food_id'=> $row['food_id'],
            'order_id' => $row['order_id'],
            'quantity' => $row['quantity']];
    }
}

$orders = [];
$users = [];
foreach ($orderItems as $index => $orderItem) {
    $result = $db->query("SELECT * FROM 'order' WHERE id = ".$orderItem['order_id']);
    foreach ($result as $row) {
        $orders[] = [
            'id' => $row['id'],
            'user_id'=> $row['user_id'],
            'order_status' => $row['order_status'],
            'total_price' => $row['total_price'],
            'created_at' => $row['created_at'],
            'deleted_at' => $row['deleted_at'],
            'note' => $row['note']];
    }

    $result = $db->query("SELECT * FROM 'users' WHERE id = ".$orders[$index]['user_id']);
    foreach ($result as $row) {
        $users[] = [
            'id' => $row['id'],
            'username'=> $row['username']];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['orderID'];
    $order_status = $_POST['status'];

    if ( $order_status == 'Teslim Edildi'){
        $deleted_at = date('d/m/Y');
        $update = $db->prepare("UPDATE 'order' SET order_status = :order_status, deleted_at = :deleted_at WHERE id = :id");
        $update->bindParam(':id', $order_id, type: PDO::PARAM_INT);
        $update->bindParam(':order_status', $order_status, PDO::PARAM_STR);
        $update->bindParam(':deleted_at', $deleted_at, PDO::PARAM_STR);
        $update->execute();
        header("Location: manageOrder.php");
    }

    else{
        $update = $db->prepare("UPDATE 'order' SET order_status = :order_status WHERE id = :id");
        $update->bindParam(':id', $order_id, type: PDO::PARAM_INT);
        $update->bindParam(':order_status', $order_status, PDO::PARAM_STR);
        $update->execute();
        header("Location: manageOrder.php");
    }
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sipariş Yönetim Paneli</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="navbarContainer">
  <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
  <h1>Sipariş Yönetim Paneli</h1>
  <div class="userList">
    <table>
      <thead>
        <tr>
            <th style="text-align: center">Kullanıcı Adı</th>
            <th style="text-align: center">Kullanıcı Adı</th>
            <th style="text-align: center">Kullanıcı Notu</th>
            <th style="text-align: center">Sipariş Durumu</th>
            <th style="text-align: center">Toplam Ödeme</th>
            <th style="text-align: center">Oluşturulma Tarihi</th>
            <th style="text-align: center">İşlemler</th>
        </tr>
    </thead>
        <button class="button" onclick=goToCompanyPanel()>Geri Dön</button>
      <tbody id="userList">
      <?php foreach ($orders as $index => $order):?>
        <form action="" method="POST">
                <tr>
                    <td style="color:black"><?php echo $order['id']; ?></td>
                    <td style="color:black"><?php echo $users[$index]['username']; ?></td>
                    <td style="color:black"><?php echo $order['note']; ?></td>
                    <td style="color:black">
                            <div class="formBox" >
                            <label for="status"></label>
                            <select name="status" id="status">
                            <option value="" selected disabled><?php echo $order['order_status']; ?></option>
                            <option value="Hazırlanıyor">Hazırlanıyor</option>
                            <option value="Yola Çıktı">Yola Çıktı</option>
                            <option value="Teslim Edildi">Teslim Edildi</option>
                            </select><br><br>
                            <div><input type="hidden" id="orderID" name="orderID" value="<?php echo $order['id']; ?>"></div>
                        </div>
                    </td>
                    <td style="color:black"><?php echo $order['total_price']; ?></td>
                    <td style="color:black"><?php echo $order['created_at']; ?></td>
                    <td style="color:black"><?php echo $order['deleted_at']; ?></td>
                    <td style="color:black"><button class="button" type="submit">Güncelle</button></td>
                </tr>
        
            </form>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  </div>
  <script src="script.js"></script>
</body>
</html>
