<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new PDO('sqlite:QuestAppDB.db');

$userID = $_SESSION['id'];
$userInfo = $db->query('SELECT * FROM user WHERE id=' . $userID);
$user = [];
foreach ($userInfo as $row) {
    $user[] = [
        'solvedquestion' => $row['solvedquestion'],
        'point' => $row['point'],
    ];
}

if (!isset($_SESSION['shuffledQuestions'])) {
    $result = $db->query('SELECT * FROM questions');
    $questions = [];
    foreach ($result as $row) {
        if (str_contains($user[0]['solvedquestion'], $row['id'])) {
            continue;
        }
        $questions[] = [
            'id' => $row['id'],
            'question' => $row['question'],
            'diff' => $row['diff'],
            'trueanswer' => $row['trueanswer'],
            'answer' => [
                ['text' => $row['answerA'], 'correct' => ($row['trueanswer'] === 'answerA')],
                ['text' => $row['answerB'], 'correct' => ($row['trueanswer'] === 'answerB')],
                ['text' => $row['answerC'], 'correct' => ($row['trueanswer'] === 'answerC')],
                ['text' => $row['answerD'], 'correct' => ($row['trueanswer'] === 'answerD')],
            ]
        ];
    }
    shuffle($questions);
    $_SESSION['shuffledQuestions'] = $questions;
    $_SESSION['currentQuestion'] = 0;
}

$currentQuestionIndex = $_SESSION['currentQuestion'];
$questions = $_SESSION['shuffledQuestions'];
$questionNumber = count($questions);

if ($questionNumber == 0 || $currentQuestionIndex >= $questionNumber) {
    echo "<script>
            alert('Çözülecek Soru Kalmadı.');
            window.location.href = 'homePage.php';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedButton'])) {
    $selectedButton = $_POST['selectedButton'];
    $trueanswer = $questions[$currentQuestionIndex]['trueanswer'];
    
    if ($selectedButton == $trueanswer) {
        $user[0]['solvedquestion'] = $user[0]['solvedquestion'] . $questions[$currentQuestionIndex]['id'] . ' ';
        $user[0]['point'] = $user[0]['point'] + $questions[$currentQuestionIndex]['diff'];

        $update = $db->prepare('UPDATE user SET solvedquestion = :solvedquestion, point = :point WHERE id = :id');
        $update->bindParam(':solvedquestion', $user[0]['solvedquestion'], PDO::PARAM_STR);
        $update->bindParam(':point', $user[0]['point'], PDO::PARAM_INT);
        $update->bindParam(':id', $userID, PDO::PARAM_INT);
        $update->execute();
        $_SESSION['currentQuestion']++;

        echo "<script>
                alert('Doğru Cevap.');
                window.location.href = 'startQuiz.php';
              </script>";
        exit();
    } else if ($selectedButton == 'next') {
        $_SESSION['currentQuestion']++;
        echo "<script>
                window.location.href = 'startQuiz.php';
              </script>";
        exit();
    } else {
        $_SESSION['currentQuestion']++;
        echo "<script>
                alert('Yanlış Cevap.');
                window.location.href = 'startQuiz.php';
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sınav</title>

  <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="navbarContainer">
        <h1>Yavuzlar Quiz Platformu</h1>
        <div><?php echo 'Point: ' . $user[0]['point']; ?></div>
        <br>
        <div class="questionText" id="questionText"><?php echo $questions[$currentQuestionIndex]['question']; ?></div>
        <div class="difficultyText" id="difficultyText">
            <?php 
                if ($questions[$currentQuestionIndex]['diff'] == 1) {
                    echo 'Kolay';
                } else if ($questions[$currentQuestionIndex]['diff'] == 3) {
                    echo 'Orta';
                } else {
                    echo 'Zor';
                }
            ?>
        </div>
        <form method="POST" action="">
            <div id="answerButton" class="btn-grid">
                <button class="button" type="submit" name="selectedButton" value="answerA"><?php echo $questions[$currentQuestionIndex]['answer'][0]['text']; ?></button>
                <button class="button" type="submit" name="selectedButton" value="answerB"><?php echo $questions[$currentQuestionIndex]['answer'][1]['text']; ?></button>
                <button class="button" type="submit" name="selectedButton" value="answerC"><?php echo $questions[$currentQuestionIndex]['answer'][2]['text']; ?></button>
                <button class="button" type="submit" name="selectedButton" value="answerD"><?php echo $questions[$currentQuestionIndex]['answer'][3]['text']; ?></button>
            </div>
            <button class="nextButton btn" type="submit" name="selectedButton" value="next">Sonraki Soru</button>
        </form>
        <button style="width: 200px; height: 50px;" id="homePageButton" onclick="goToHomePage()">Anasayfa</button>
    </div>
    <script src="script.js"></script>
</body>

</html>
