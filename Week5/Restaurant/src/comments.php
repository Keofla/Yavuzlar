<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new PDO('sqlite:restaurant.db');

$user_id = '';
$restaurantID = '';
if (isset($_GET["id"])) {
  $user_id = $_SESSION['id'];
  $restaurantID = $_GET["id"];
}

$result = $db->query('SELECT * FROM restaurant WHERE id = '. $restaurantID);
$restaurant = [];
foreach ($result as $row) {
    $restaurant = [
        'name'=> $row['name']
    ];
}

$result = $db->query('SELECT * FROM comments WHERE restaurant_id = '. $restaurantID.' ORDER BY id DESC');
$comments = [];
foreach ($result as $row) {
    $comments[] = [
        'user_id' => $row['user_id'],
        'restaurant_id'=> $row['restaurant_id'],
        'title' => $row['title'],
        'description'=> $row['description'],
        'score'=> $row['score'],
        'created_at'=> $row['created_at']
    ];
}

$result = $db->query('SELECT * FROM users');
$users = [];
foreach ($result as $row) {
    $users[] = [
        'id' => $row['id'],
        'username'=> $row['username']
    ];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $score = $_POST['score'];
    $created_at = date('d/m/Y');
    

    $update = $db->prepare("INSERT INTO comments (user_id, restaurant_id, title, description, score, created_at) 
    VALUES (:user_id, :restaurant_id, :title, :description, :score, :created_at)");
    $update->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $update->bindParam(':restaurant_id', $restaurantID, PDO::PARAM_INT);
    $update->bindParam(':description', $description, PDO::PARAM_STR);
    $update->bindParam(':title', $title, PDO::PARAM_STR);
    $update->bindParam(':score', $score, PDO::PARAM_STR);
    $update->bindParam(':created_at', $created_at, PDO::PARAM_STR);
    $update->execute();
    header("Location: comments.php?id=$restaurantID");
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
      <img src="images/logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;"><br><br>
      <h1><?php echo $restaurant['name'];?></h1>
      <button class="button" onclick="gotoMainMenu()">Geri Dön</button>
  </div><br><br>
  <form action="" method="POST" >
        <div class="formBox">
            <div>Başlık: </div>
            <input type="text" id="title" name="title" placeholder="Başlık" value="" required>
        </div><br><br>
        <div class="formBox">
            <div>Açıklama: </div>
            <input type="text" id="description" name="description" placeholder="Açıklama" value="" required>
        </div><br><br>
        <div class="formBox" >
        <label for="score">Puan:</label>
        <select name="score" id="score" required>
          <option value="" selected disabled>Puan Seçiniz</option>
          <?php for ($i = 1; $i <= 10; $i++): ?>
                  <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
              <?php endfor; ?>
        </select><br><br>
      </div>
      <div class="formBox">
            <button name="addComment" type="submit">Yorum Yap</button>
        </div>
    </form><br><br>
        <div><?php foreach ($comments as $index => $comment):?>
            <?php foreach ($users as $index => $user):?>
                <?php if ($comment['user_id'] == $user['id']):?>
                <div class="comment">
                <div class="username"><?php echo $user['username'];?></div>
                <div class="created_at"><?php echo $comment['created_at'];?></div>
                <div class="title"><?php echo $comment['title'];?></div>
                <div class="description"><?php echo $comment['description'];?></div>
                <div class="score"><?php echo $comment['score'];?></div><br><br>
                </div>
                <?php endif;?>
            <?php endforeach;?>
        <?php endforeach;?>
    </div>
  <script src="script.js"></script>
</body>
</html>