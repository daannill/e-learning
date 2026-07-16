<?php
    $nextIndex = max(array_keys($oldQuestions)) + 1;
?>

<div class="quiz-question-list" id="quizQuestionList" data-next-index="<?= $nextIndex ?>">
    <?php foreach ($oldQuestions as $index => $q) : ?>
        <div class="quiz-question <?= hasFlash("errors.quiz.$index.correct") ? 'error' : '' ?>" data-question-index="<?= $index ?>">
            <div class="quiz-question-header">
                <span class="quiz-question-number">Question <?= $index ?></span>
                <button type="button" class="quiz-remove-btn" onclick="removeQuizQuestion(this)" aria-label="Remove question">
                    <svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg>
                </button>
            </div>
            <?php include APP_PATH . '/app/views/partials/quiz_question_fields.php'; ?>
        </div>
    <?php endforeach; ?>
</div>

<button type="button" class="quiz-add-question-btn" onclick="addQuizQuestion()">
    <svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg>
    Add Question
</button>

<template id="quizQuestionTemplate">
    <div class="quiz-question" data-question-index="__INDEX__">
        <div class="quiz-question-header">
            <span class="quiz-question-number">Question __INDEX__</span>
            <button type="button" class="quiz-remove-btn" onclick="removeQuizQuestion(this)" aria-label="Remove question">
                <svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg>
            </button>
        </div>
        <?php $index = '__INDEX__'; $q = []; include APP_PATH . '/app/views/partials/quiz_question_fields.php';; ?>
    </div>
</template>