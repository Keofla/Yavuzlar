const questionListButton = document.getElementById("questionListButton");
const startQuizButton = document.getElementById("startButton");
const nextQuizButton = document.getElementById("nextButton");
const questionContainer = document.getElementById("question");
const questionElement = document.getElementById("questionText");
const difficultyElement = document.getElementById("difficultyText");
const answerButtonGrid = document.getElementById("answerButton")

let shuffledQuestions, currentQuestionIndex, score, point;
let questionsList = [];

function goToStartQuiz() {
  window.location.href = "startQuiz.html";
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

function loadQuestions() {
  fetch('questions.json')
  .then(response => response.json())
  .then(data => {
    questionsList = data;
    listQuestions();
  });
}

function gotoQuestionList() { 
  window.location.href = "questions.html";
}

function goToAddQuestion() { 
  window.location.href = "addQuestion.html";
  console.log(questionsList);
}

const addQuestion = (ev)=>{
  let question = {
    question: document.getElementById('question').value,
    difficulty: document.getElementById('diff').value,
    answer: [{text: document.getElementById('answer1').value, correct: document.getElementById('true1').value},
            {text: document.getElementById('answer2').value, correct: document.getElementById('true2').value},
            {text: document.getElementById('answer3').value, correct: document.getElementById('true3').value},
            {text: document.getElementById('answer4').value, correct: document.getElementById('true4').value}
    ]
  }
  questionsList.push(question);
  document.forms[0].reset();
  alert(`added ${questionsList[questionsList.length -1].question}`);
  console.log(questionsList);
}

function goToModifyQuestion(index) { 
  window.location.href = `modifyQuestion.html?index=${index}`;
}

const modifyQuestion = (ev)=>{
  const urlParams = new URLSearchParams(window.location.search);
  index = urlParams.get('index');
  let question = {
    question: document.getElementById('question').value,
    difficulty: document.getElementById('diff').value,
    answer: [{text: document.getElementById('answer1').value, correct: document.getElementById('true1').value},
            {text: document.getElementById('answer2').value, correct: document.getElementById('true2').value},
            {text: document.getElementById('answer3').value, correct: document.getElementById('true3').value},
            {text: document.getElementById('answer4').value, correct: document.getElementById('true4').value}
    ]
  }
  questionsList[index] = question;
  document.forms[0].reset();
  console.log(questionsList);
}


function goToHomePage() {
  window.location.href = "homePage.html";
}

function deleteQuestion(index) {
  questionsList.splice(index, 1);
  listQuestions();
}

function listQuestions() {
  const questionListElement = document.getElementById('questionList');
  questionListElement.innerHTML = '';
  
  questionsList.forEach((item, index) => {
    const row = document.createElement('tr');
    
    const idCell = document.createElement('td');
    idCell.textContent = index + 1;
    row.appendChild(idCell);

    const questionCell = document.createElement('td');
    questionCell.textContent = item.question;
    row.appendChild(questionCell);

    const difficultyCell = document.createElement('td');
    if(item.difficulty == 1){
      difficultyCell.textContent = 'Kolay';
    }
    else if(item.difficulty == 3){
      difficultyCell.textContent = 'Orta';
    }
    else{
      difficultyCell.textContent = 'Zor'
    }
    row.appendChild(difficultyCell);

    const actionsCell = document.createElement('td');
    actionsCell.innerHTML = `
      <button style= "width: 100px; height: 50px" onclick="goToModifyQuestion(${index});">Düzenle</button>
      <button style= "width: 100px; height: 50px" onclick="deleteQuestion(${index})">Sil</button>`;
    row.appendChild(actionsCell);

    questionListElement.appendChild(row);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  loadQuestions();
});

document.addEventListener('DOMContentLoaded', () => {
  const addQuestionButton = document.getElementById('addQuestion');
  addQuestionButton.addEventListener('click', (ev) => {
    ev.preventDefault();
    addQuestion(ev);
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const addQuestionButton = document.getElementById('modifyQuestion');
  addQuestionButton.addEventListener('click', (ev) => {
    ev.preventDefault();
    modifyQuestion(ev);
  });
});

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
*/
