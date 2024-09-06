<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: homePage.php");
    exit();
}

$db = new PDO('sqlite:QuestAppDB.db');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $question = $_POST['question'];
  $answerA = $_POST['answer1'];
  $answerB = $_POST['answer2'];
  $answerC = $_POST['answer3'];
  $answerD = $_POST['answer4'];
  $trueanswer = $_POST['correct'];
  $diff = $_POST['diff'];

  $update = $db->prepare('INSERT INTO questions (question, answerA, answerB, answerC, answerD, trueanswer, diff) 
        VALUES(:question, :answerA, :answerB, :answerC, :answerD, :trueanswer, :diff)');
  
  $update->bindParam(':question', $question, PDO::PARAM_STR);
  $update->bindParam(':answerA', $answerA, PDO::PARAM_STR);
  $update->bindParam(':answerB', $answerB, PDO::PARAM_STR);
  $update->bindParam(':answerC', $answerC, PDO::PARAM_STR);
  $update->bindParam(':answerD', $answerD, PDO::PARAM_STR);
  $update->bindParam(':trueanswer', $trueanswer, PDO::PARAM_STR);
  $update->bindParam(':diff', $diff, PDO::PARAM_INT);
  
  $update->execute();
  header("Location: questions.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Soru Ekle</title>

  <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navbarContainer">
      <h2 >Soru Ekle</h2>
      <form action="" method="POST">
      <div class="formBox">
        <input type="text" id="question" name="question" placeholder="Soru" required><br><br>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer1" name="answer1" placeholder="Şık A" required>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer2" name="answer2" placeholder="Şık B" required>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer3" name="answer3" placeholder="Şık C" required>
      </div><br><br>
      <div class="formBox">
        <input type="text" id="answer4" name="answer4" placeholder="Şık D" required>
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
        <button style="width: 200px; height: 50px; " name="addQuestion" type="submit">Soru Ekle</button>
      </div>
    </form>
      <button style="width: 200px; height: 50px; " id="homePageButton" onclick="goToHomePage()">Anasayfa</button>
    </div>

  <script src="script.js"></script>
</body>

</html>