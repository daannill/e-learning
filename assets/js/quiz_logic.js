const startQuizBtn = document.getElementById('startQuizBtn');
const quizState = document.getElementById('quiz-state');
const quizContainer = document.getElementById('quiz-container');
const timerElement = document.getElementById('quizTimer');
const quizForm = document.getElementById('quiz-form');
const answeredCount = document.getElementById('answeredCount');
const progressFill = document.getElementById('progressFill');
const totalQuestions = Number(quizContainer.dataset.totalQuestions);
const timerFloat = document.getElementById('timerFloat');
const answeredFloat = document.getElementById('answeredFloat');
const quizAlert = document.getElementById('quizAlert');

const btnTryAgain = document.getElementById('btnTryAgain');

let quizStarted = false;

if (startQuizBtn) {
    startQuizBtn.addEventListener('click', startQuiz);
}

if(btnTryAgain) {

    const nextAttempt = Number(btnTryAgain.dataset.nextAttempt);
    const used = Number(btnTryAgain.dataset.attemptsUsed);
    const max = Number(btnTryAgain.dataset.maxAttempts);

    btnTryAgain.addEventListener('click', startQuiz);

    setInterval(updateButton, 1000);

    function updateButton() {

        const now = Math.floor(Date.now() / 1000);

        if (used >= max) {

            if (now <= nextAttempt) {

                const diff = nextAttempt - now;

                const minutes = Math.floor(diff / 60);
                const seconds = diff % 60;

                btnTryAgain.disabled = true;
                btnTryAgain.textContent = `Try Again in ${minutes}m ${seconds}s`;

                return;
            }

            btnTryAgain.disabled = false;
            btnTryAgain.textContent = "Try Again";
        }
    }
}

quizContainer.addEventListener('change', answerProgress);

quizForm.addEventListener('submit', validate);

function validate(event) {

    const questionCards = document.querySelectorAll('.quiz-card');

    let firstUnanswered = null;

    questionCards.forEach(card => {

        card.classList.remove('missing-answer');

        const radios =
            card.querySelectorAll(
                'input[type="radio"]'
            );

        let answered = false;

        radios.forEach(radio => {

            if(radio.checked){
                answered = true;
            }

        });

        if(!answered && !firstUnanswered){
            firstUnanswered = card;
        }
    });

    if(firstUnanswered){

        event.preventDefault();

        firstUnanswered.classList.add(
            'missing-answer'
        );

        firstUnanswered.scrollIntoView({

            behavior:'smooth',

            block:'center'
        });

    }
}

function answerProgress(event) {

    if (!event.target.matches('.options input[type="radio"]')) {
        return;
    }

    updateProgress();
}

async function startQuiz() {

    quizStarted = true;

    quizState.hidden = true;
    quizContainer.hidden = false;

    await loadQuestions();

    startTimer();

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function updateTimer(seconds) {

    const minutes = Math.floor(seconds / 60);
    const remainSeconds = seconds % 60;

    timerFloat.textContent = 
        `⏱ ${String(minutes).padStart(2, '0')}:${String(remainSeconds).padStart(2, '0')}`;

    timerElement.textContent =
        `⏱ ${String(minutes).padStart(2, '0')}:${String(remainSeconds).padStart(2, '0')}`;
}

function startTimer() {

    const timerMinutes = Number(quizContainer.dataset.timer);

    if (!timerMinutes) {
        return;
    }

    let seconds = timerMinutes * 60;

    updateTimer(seconds);

    const interval = setInterval(() => {

        seconds--;

        updateTimer(seconds);

        if (seconds === 10) {

            showAlert(
                '⚠️ Only 10 seconds remaining!'
            );

            timerFloat.classList.add('danger');
        }

        if (seconds <= 0) {

            clearInterval(interval);

            showAlert(
                'Time is up! Your quiz will be submitted.'
            );

            setTimeout(() => {
                quizForm.submit();
            }, 1000);
        }

    }, 1000);
}

async function loadQuestions(quizId) {

    try {

        const response = await fetch(
            `/elearning/api/questions/${quizContainer.dataset.quizId}`
        );

        if (!response.ok) {
            throw new Error('Failed to load questions');
        }

        const questions = await response.json();

        renderQuestions(questions);

    } catch (error) {

        console.error(error);

        alert('Failed to load quiz questions');
    }
}

function renderQuestions(questions) {

    const container =
        document.getElementById(
            'questions-container'
        );

    container.innerHTML = '';

    questions.forEach(question => {

        let optionsHtml = '';

        question.options.forEach(option => {

            optionsHtml += `
                <label class="option">

                    <input
                        type="radio"
                        name="answers[${question.question_id}]"
                        value="${option.option_id}"
                    >

                    <span class="option-text">
                        ${option.option_text}
                    </span>

                </label>
            `;
        });

        container.innerHTML += `
            <div class="quiz-card" id="question-${question.question_id}">

                <div class="question-number">
                    Question ${question.question_order}
                </div>

                <h3 class="question-title">
                    ${question.question}
                </h3>

                <div class="options">
                    ${optionsHtml}
                </div>

            </div>
        `;
    });

}

function updateProgress() {

    const answeredQuestions = new Set();

    document
        .querySelectorAll(
            '.options input[type="radio"]:checked'
        )
        .forEach(radio => {

            answeredQuestions.add(
                radio.name
            );

        });

    const count = answeredQuestions.size;

    answeredFloat.textContent = `${count} / ${totalQuestions}`;

    answeredCount.textContent = `${count} / ${totalQuestions}`;

    progressFill.style.width =`${(count / totalQuestions) * 100}%`;
}

function showAlert(message) {

    quizAlert.textContent = message;

    quizAlert.classList.add('show');

    setTimeout(() => {
        quizAlert.classList.remove('show');
    }, 3000);

}