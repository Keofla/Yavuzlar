const questionListButton = document.getElementById("questionListButton");
const startQuizButton = document.getElementById("startButton");
const nextQuizButton = document.getElementById("nextButton");
const questionContainer = document.getElementById("question");
const questionElement = document.getElementById("questionText");
const difficultyElement = document.getElementById("difficultyText");
const answerButtonGrid = document.getElementById("answerButton")

let shuffledQuestions, currentQuestionIndex, score, point;

function goToStartQuiz() {
  window.location.href = "startQuiz.php";
}

function startQuiz() {
  startQuizButton.classList.add('hide');
  shuffledQuestions = questionsList.sort(() => Math.random() - 0.5);
  console.log(shuffledQuestions)
  currentQuestionIndex = 0;
  score = 0;
  questionContainer.classList.remove('hide');
  nextQuestion();
}

function nextQuestion() {
  resetStat();
  showQuestion(shuffledQuestions [currentQuestionIndex])
}

function resetStat() {
  clearStatusClass(document.body)
  nextQuizButton.classList.add('hide');
  while (answerButtonGrid.firstChild){
    answerButtonGrid.removeChild(answerButtonGrid.firstChild)
  }
}

function showQuestion(question) {
  point = question.difficulty;
  questionElement.innerText = question.question
  if(point == 1){
    difficultyElement.innerText = 'Kolay'
  }
  else if(point == 3){
    difficultyElement.innerText = 'Orta'
  }
  else{
    difficultyElement.innerText = 'Zor'
  }
  
  question.answer.forEach(answer => {
    const button = document.createElement('button')
    button.innerText = answer.text
    button.classList.add('btn')
    if (answer.correct){
      button.dataset.correct = answer.correct;
    }
    button.addEventListener('click', selectAnswer);
    answerButtonGrid.appendChild(button);
  })
}

function selectAnswer(e) {
  const selectedButton = e.target;
  const correct = selectedButton.dataset.correct === "true";
  setStatusClass(document.body, correct);

  Array.from(answerButtonGrid.children).forEach(button => {
    setStatusClass(button, button.dataset.correct === "true");
    button.disabled = true;
  });
  if (correct) {
    score = score + point;
  }
  if (shuffledQuestions.length > currentQuestionIndex + 1) {
    nextQuizButton.classList.remove('hide');
  } else {
    showScore();
  }
}

function showScore() {
  resetStat();
  questionElement.innerHTML = `Puanın: ${score}`;
  difficultyElement.innerText = ''
  startQuizButton.innerText = 'Tekrar Başla';
  startQuizButton.classList.remove('hide');
}

function setStatusClass(element, correct){
  clearStatusClass(element);
  if (correct){
    element.classList.add('correct');
  }
  else{
    element.classList.add('wrong');
  }
}

function clearStatusClass(element){
  element.classList.remove('correct')
  element.classList.remove('wrong')
}

function gotoQuestionList() { 
  window.location.href = "questions.php";
}

function goToAddQuestion() { 
  window.location.href = "addQuestion.php";
  console.log(questionsList);
}

function goToModifyQuestion(index) { 
  window.location.href = `modifyQuestion.php?index=${index}`;
}

function goToHomePage() {
  window.location.href = 'homePage.php';
}

function goToLoginPage() {
  window.location.href = "login.php";
}

function goToRegisterPage() {
  window.location.href = "register.php";
}

function logout() {
  window.location.href = "logout.php"
}

startQuizButton.addEventListener('click', startQuiz)

nextQuizButton.addEventListener('click', () => {
  currentQuestionIndex++
  nextQuestion()
})

/*Referanslar 
https://www.youtube.com/watch?v=w1Oz0Sj1QyQ
https://dizzpy.medium.com/how-to-connect-html-with-json-using-javascript-a-beginners-guide-25e94306fa0f
https://www.geeksforgeeks.org/how-to-delete-an-index-from-json-object/
https://www.youtube.com/watch?v=P7h80B-1ifA
https://www.youtube.com/watch?v=NxVCq4p0Kb0
https://olatade.medium.com/how-to-convert-mysql-query-result-to-json-in-php-5f727e566dd9
https://www.youtube.com/watch?v=I2lB7fZE37g
https://www.sitepoint.com/community/t/using-a-button-to-update-a-cell-in-database/367689/2
https://www.w3schools.com/sql/sql_insert.asp

*/