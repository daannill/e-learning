let quizQuestionCounter = 1;

document.addEventListener('DOMContentLoaded', function () {
    updateMinimumCorrect();
});

function addQuizQuestion() {
    const list = document.getElementById('quizQuestionList');
    const index = list.children.length + 1;

    const template = document.getElementById('quizQuestionTemplate');
    const clone = template.content.cloneNode(true);
    const block = clone.querySelector('.quiz-question');

    block.dataset.questionIndex = index;

    block.querySelectorAll('[name]').forEach(function (field) {
        field.name = field.name.replace('__INDEX__', index);
    });

    block.querySelector('.quiz-question-number').textContent = 'Question ' + index;

    list.appendChild(clone);

    updateMinimumCorrect();
}

function removeQuizQuestion(button) {
    const list = document.getElementById('quizQuestionList');
    if (list.children.length <= 5) return;

    button.closest('.quiz-question').remove();
    updateQuestionNumbers();
    updateMinimumCorrect();
}

function updateQuestionNumbers() {
    document.querySelectorAll('#quizQuestionList .quiz-question').forEach(function (block, i) {
        const newIndex = i + 1;
        const oldIndex = block.dataset.questionIndex;

        block.querySelector('.quiz-question-number').textContent = 'Question ' + newIndex;
        block.dataset.questionIndex = newIndex;

        block.querySelectorAll('[name]').forEach(function (field) {
            field.name = field.name.replace('quiz[' + oldIndex + ']', 'quiz[' + newIndex + ']');
        });
    });
}

function updateMinimumCorrect() {
    const totalQuestions = document.querySelectorAll('#quizQuestionList .quiz-question').length;
    console.log(totalQuestions);

    const input = document.getElementById('minimum_correct');
    const hint = document.getElementById('minimumCorrectHint');

    input.max = totalQuestions;

    if (Number(input.value) > totalQuestions) {
        input.value = totalQuestions;
    }

    hint.textContent = `Out of ${totalQuestions} questions.`;
}