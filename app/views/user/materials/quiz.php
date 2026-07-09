<?php
    
$title = 'Quiz';

$styles = ['pages/user/quiz', 'components/navbar', 'components/button', 'components/material_sidebar'];

$scripts = ['user_open', 'material_sidebar_open', 'quiz_logic'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?> 

<div class="learning-layout">

    <?php require 'app/views/layouts/material_sidebar.php' ?> 

    <!-- CONTENT -->

    <main class="content">

        <div class="state-card" id="quiz-state">

            <?php if (!isset($userQuizProgress['attempt_id'])) : ?>

                <span class="quiz-badge badge-new">
                    📝 Ready to Start
                </span>

            <?php elseif ($userQuizProgress['best_score'] >=  $quizInfo['passing_score']) : ?>

                <span class="quiz-badge badge-pass">
                    ✅ Passed
                </span>

            <?php else : ?>

                <span class="quiz-badge badge-fail">
                    ❌ Not Passed
                </span>

            <?php endif; ?>

            <h2 class="quiz-title">
                <?= $materialInfo['title'] ?>
            </h2>

            <p class="quiz-description">
                Test your understanding of the material before continuing
                to the next lesson.
            </p>

            <?php if ($userQuizProgress['is_passed']) : ?>

                <div class="score-box">

                    <div class="score-left">

                        <h3>
                            Congratulations!
                        </h3>

                        <p>
                            You have passed this quiz and may continue learning.
                        </p>

                    </div>

                    <div class="score-circle pass">
                        <?= $userQuizProgress['score'] ?>%
                    </div>

                </div>

            <?php elseif(isset($userQuizProgress['attempt_id'])) : ?>

                <div class="score-box">

                    <div class="score-left">

                        <h3>
                            Latest Attempt
                        </h3>

                        <p>
                            You need at least <?= $quizInfo['passing_score'] ?>% to pass this quiz.
                        </p>

                    </div>

                    <div class="score-circle fail">
                        <?= $userQuizProgress['score'] ?>%
                    </div>

                </div>

            <?php endif; ?>

            <div class="info-grid">

                <?php if (isset($userQuizProgress['attempt_id'])) : ?>

                    <div class="info-card">
                        <span class="info-label">Best Score</span>
                        <span class="info-value <?= $userQuizProgress['score'] >= $quizInfo['passing_score'] ? 'success' : 'danger' ?>"><?= $userQuizProgress['best_score'] ?>%</span>
                    </div>

                <?php endif; ?>

                <div class="info-card">
                    <span class="info-label">Questions</span>
                    <span class="info-value"><?= $quizInfo['total_questions'] ?></span>
                </div>

                <?php if (!isset($userQuizProgress['attempt_id'])) : ?>

                    <div class="info-card">
                        <span class="info-label">Passing Score</span>
                        <span class="info-value"><?= $quizInfo['passing_score'] ?>%</span>
                    </div>

                <?php endif; ?>
                

                <div class="info-card">
                    <span class="info-label">Attempts</span>
                    <span class="info-value"><?= $quizInfo['attempts_used'] ?> / <?= $quizInfo['max_attempts'] ?></span>
                </div>

                <div class="info-card">
                    <span class="info-label">Time Limit</span>
                    <span class="info-value"><?= $quizInfo['timer'] ?></span> m
                </div>

            </div>

            <div class="actions">

                <?php if (isset($isQuizMaterial)) : ?>

                    <?php if ($prevMaterial) : ?>
                        
                        <a href="<?= BASEURL . '/material/' . $prevMaterial ?>" class="btn btn-secondary">
                            ← Previous Lesson
                        </a>
                        
                    <?php endif; ?>
                    
                <?php else : ?>

                    <a href="<?= BASEURL . '/material/' . $materialInfo['material_id'] ?>" class="btn btn-secondary">
                        ← Back to Material
                    </a>
                    
                <?php endif; ?>
                

                <div class="right-actions">

                    <?php if (!isset($userQuizProgress['attempt_id'])) : ?>

                        <button
                            type="button"
                            class="btn btn-primary"
                            id="startQuizBtn"
                        >
                            Start Quiz
                        </button>

                    <?php elseif ($quizInfo['attempts_used'] >= $quizInfo['max_attempts']) : ?>

                        <a
                            type="button"
                            class="btn btn-warning"
                            id="btnTryAgain"
                            data-next-attempt= <?= $quizInfo['next_attempt_at'] ?>
                            data-attempts-used= <?= $quizInfo['attempts_used'] ?>
                            data-max-attempts= <?= $quizInfo['max_attempts'] ?>
                            disabled
                        >
                            Try Again ...
                        </a>

                    <?php else : ?>

                        <button
                            type="button"
                            class="btn btn-warning"
                            id="startQuizBtn"
                        >
                            Retry 
                        </button>

                    <?php endif; ?>

                    <?php if ($userQuizProgress['best_score'] >= $quizInfo['passing_score'] ) : ?>
                        <a href="<?= BASEURL . '/material/' . $nextMaterial ?>" class="btn btn-primary">Next Material →</a>
                    <?php endif; ?>

                </div>

            </div>

        </div>

        <div id="quiz-container" data-total-questions="<?= $quizInfo['total_questions'] ?>" data-quiz-id="<?= $quizInfo['quiz_id'] ?>" data-timer="<?= $quizInfo['timer'] ?>" hidden>
            
            <form action="<?= BASEURL . '/quiz/submit' ?>" method="POST" id="quiz-form">

                <?= csrf() ?>

                <input type="hidden" name="user_progress_id" value="<?= $userProgress ?>">
                <input type="hidden" name="quiz_id" value="<?= $quizInfo['quiz_id'] ?>">
                <input type="hidden" name="course_id" value="<?= $courseId ?>">
                <input type="hidden" name="enrollment_id" value="<?= $enrollmentId ?>">
                <input type="hidden" name="material_id" value="<?= $currentMaterial ?>">

                <!-- HEADER -->

                <div class="quiz-header">

                    <div id="quizAlert" class="quiz-alert"></div>

                    <span class="quiz-badge">
                        Quiz Material
                    </span>

                    <h1 class="quiz-title">
                        <?= $materialInfo['title'] ?>
                    </h1>

                    <p class="quiz-description">
                        Answer the following questions based on the material
                        you have learned. Make sure you choose the best answer
                        before submitting.
                    </p>

                    <div class="quiz-meta">

                        <span>
                            <?= $quizInfo['total_questions'] ?> Questions
                        </span>

                        <span>
                            Passing Score <?= $quizInfo['passing_score'] ?>%
                        </span>

                        <span>
                            Attempt <?= $quizInfo['attempts_used'] ?>/<?= $quizInfo['max_attempts'] ?>
                        </span>

                        <span id="quizTimer">
                            ⏱ 10:00
                        </span>

                    </div>

                    <div class="quiz-progress">

                        <div class="progress-info">

                            <span>
                                Answered
                            </span>

                            <span id="answeredCount">
                                0 / <?= $quizInfo['total_questions'] ?>
                            </span>

                        </div>

                        <div class="progress-bar">

                            <div
                                id="progressFill"
                                class="progress-fill"
                            ></div>

                        </div>

                    </div>

                </div>

                <div class="quiz-floating-info">

                    <div class="floating-row">

                        <span class="floating-icon">
                            ⏱
                        </span>

                        <div>

                            <div class="floating-label">
                                Time Remaining
                            </div>

                            <div id="timerFloat" class="floating-value">
                                10:00
                            </div>

                        </div>

                    </div>

                    <div class="floating-row">

                        <span class="floating-icon">
                            ✓
                        </span>

                        <div>

                            <div class="floating-label">
                                Answered
                            </div>

                            <div id="answeredFloat" class="floating-value">
                                0 / <?= $quizInfo['total_questions'] ?>
                            </div>

                        </div>

                    </div>

                </div>

                <div id="questions-container"></div>

                <!-- FOOTER -->

                <div class="quiz-footer">

                    <div class="quiz-actions" style="margin-left: auto;">

                        <button id="submit-quiz" class="btn btn-primary">
                            Submit
                        </button>

                        <!-- <a href="#" class="btn btn-primary">
                            Next Material →
                        </a> -->

                    </div>

                </div>

            </form>

        </div>

    </main>

</div>

<?php require 'app/views/layouts/footer.php' ?> 