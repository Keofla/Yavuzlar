<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$_SESSION['shuffledQuestions'] = null;
$_SESSION['currentQuestion'] = 0;

$db = new PDO('sqlite:QuestAppDB.db');
$result = $db->query('SELECT * FROM user ORDER BY point DESC');
$users = [];
foreach ($result as $row) {
    $users[] = [
        'id'=> $row['id'],
        'username' => $row['username'],
        'point' => $row['point']
    ];
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
      <img src="logo.png" alt="image" style="margin-left: 5%; width: 550px; height: auto;">
      <h1>Yavuzlar Quiz Platformu</h1>
    </div>
    <div class="questionList">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Kullanıcı Adı</th>
          <th>Puan</th>
        </tr>
      </thead>
      <tbody id="questionList">
        <?php foreach ($users as $index => $user): ?>
          <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo $user['username']; ?></td>
            <td><?php echo $user['point']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
    <div class="navbar">
      <button class="button" id="goToQuizButton" onclick="goToStartQuiz()">Sınavı Başlat</button>
      <button class="button" id="questionListButton" onclick="gotoQuestionList()">Sorular</button>
      <button class="button" id="logoutButton" onclick="logout()">Çıkış yap</button>
    </div>
  </div>
  </div>
  </div>
  <script src="script.js"></script>
</body>
</html>