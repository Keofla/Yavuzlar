<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new PDO('sqlite:QuestAppDB.db');
$result = $db->query('SELECT * FROM questions');
$questions = [];
foreach ($result as $row) {
    $questions[] = [
        'id'=> $row['id'],
        'question' => $row['question'],
        'difficulty' => $row['diff'],
        'answer' => [
            ['text' => $row['answerA'], 'correct' => ($row['trueanswer'] === 'answerA')],
            ['text' => $row['answerB'], 'correct' => ($row['trueanswer'] === 'answerB')],
            ['text' => $row['answerC'], 'correct' => ($row['trueanswer'] === 'answerC')],
            ['text' => $row['answerD'], 'correct' => ($row['trueanswer'] === 'answerD')],
        ]
    ];
}

if(isset($_GET['id'])) {
  $id = $_GET['id'];
  $del = $db->query('DELETE FROM questions WHERE id='.$id);
  header("Location: questions.php");
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
    <h1>Yavuzlar Quiz Platformu</h1>
    <button class="button" id="homePageButton" onclick="goToHomePage()">Anasayfa</button>
    <?php if ($_SESSION['role'] == 'admin'): ?>
      <button class="button" id="addQuestionButton" onclick="goToAddQuestion()">Soru Ekle</button>
    <?php endif; ?>
  
  <div class="questionList">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Soru</th>
          <th>Zorluk</th>
          <?php if ($_SESSION['role'] == 'admin'): ?>
          <th>İşlemler</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody id="questionList">
        <?php foreach ($questions as $index => $question): ?>
          <tr>
            <td><?php echo $question['id']; ?></td>
            <td><?php echo $question['question']; ?></td>
            <td>
              <?php
              if ($question['difficulty'] == 1) {
                echo 'Kolay';
              } 
              else if ($question['difficulty'] == 3) {
                echo 'Orta';
              }
              else{
                echo 'Zor';
              }
              ?>
            </td>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <td>
                <button class="button" onclick="window.location.href='modifyQuestion.php?id=<?php echo $question['id']; ?>'">Düzenle</button>
                <button class="button" onclick="window.location.href='questions.php?id=<?php echo $question['id']; ?>'">Sil</button>
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
