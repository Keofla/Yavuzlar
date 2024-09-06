<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: homePage.php");
    exit();
}

$id = '';
if(isset($_GET['id'])) {
  $id = $_GET['id'];
}

$db = new PDO('sqlite:QuestAppDB.db');
$result = $db->query('SELECT * FROM questions WHERE id='.$id);
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $question = $_POST['question'];
  $answerA = $_POST['answer1'];
  $answerB = $_POST['answer2'];
  $answerC = $_POST['answer3'];
  $answerD = $_POST['answer4'];
  $trueanswer = $_POST['correct'];
  $diff = $_POST['diff'];

  $update = $db->prepare('UPDATE questions 
        SET question = :question, answerA = :answerA, answerB = :answerB, 
        answerC = :answerC, answerD = :answerD, trueanswer = :trueanswer, diff = :diff 
        WHERE id = :id');
  
  $update->bindParam(':question', $question, PDO::PARAM_STR);
  $update->bindParam(':answerA', $answerA, PDO::PARAM_STR);
  $update->bindParam(':answerB', $answerB, PDO::PARAM_STR);
  $update->bindParam(':answerC', $answerC, PDO::PARAM_STR);
  $update->bindParam(':answerD', $answerD, PDO::PARAM_STR);
  $update->bindParam(':trueanswer', $trueanswer, PDO::PARAM_STR);
  $update->bindParam(':diff', $diff, PDO::PARAM_INT);
  $update->bindParam(':id', $id, PDO::PARAM_INT);
  
  $update->execute();
  header("Location: questions.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soru Değiştir</title>

  <link rel="stylesheet" href="style.css">

  <style>

  </style>

</head>

<body>
  <div class="navbarContainer">
    <h2 >Soru Düzenle</h2>
    <form action="" method="POST">
      <div class="formBox">
        <input type="text" id="question" name="question" placeholder="Soru" value="<?php echo $questions[0]['question'];?>" required><br><br>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer1" name="answer1" placeholder="Şık A" value="<?php echo $questions[0]['answer'][0]['text'];?>" required>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer2" name="answer2" placeholder="Şık B" value="<?php echo $questions[0]['answer'][1]['text'];?>" required>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer3" name="answer3" placeholder="Şık C" value="<?php echo $questions[0]['answer'][2]['text'];?>" required>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer4" name="answer4" placeholder="Şık D" value="<?php echo $questions[0]['answer'][3]['text'];?>" required>
      </div><br><br>
      <div class="formBox" >
        <label for="correct">Doğru Şık:</label>
        <select name="correct" id="correct" required>
          <option value="" selected disabled>Şık Seçiniz</option>
          <option value="answerA">A</option>
          <option value="answerB">B</option>
          <option value="answerC">C</option>
          <option value="answerD">D</option>
        </select><br><br>
      </div>
      <div class="formBox" >
        <label for="diff">Zorluk:</label>
        <select name="diff" id="diff" required>
          <option value="" selected disabled>Zorluk Seçiniz</option>
          <option value=1>Kolay</option>
          <option value=3>Orta</option>
          <option value=5>Zor</option>
        </select><br><br>
      </div>
      <div class="formBox">
        <button style="width: 200px; height: 50px; " name="modifyQuestion" type="submit">Soru Düzenle</button>
      </div>
    </form>
    <button style="width: 200px; height: 50px; " id="homePageButton" onclick="goToHomePage()">Anasayfa</button>
  </div>

<script src="script.js"></script>
</body>

</html>