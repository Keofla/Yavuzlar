<?php
    $db = new PDO('sqlite:QuestAppDB.db');
    $result = $db->query('SELECT * FROM questions');
    $questions = [];
    foreach ($result as $row) {
        $questions[] = [
            'question' => $row['question'],
            'diff' => $row['diff'],
            'answer' => [
                            ['text' => $row['answerA'], 'correct' => ($row['trueanswer'] === 'answerA')],
                            ['text' => $row['answerB'], 'correct' => ($row['trueanswer'] === 'answerB')],
                            ['text' => $row['answerC'], 'correct' => ($row['trueanswer'] === 'answerC')],
                            ['text' => $row['answerD'], 'correct' => ($row['trueanswer'] === 'answerD')],
                        ]
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($questions);
?>
