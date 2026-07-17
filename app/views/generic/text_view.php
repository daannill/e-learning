    <?php

    $title = $material['title'] ?? 'View Material';

    $styles = [
        'components/button',
        'components/teacher_sidebar',
        'components/teacher_layout',
        'components/form',
        'components/quiz_view',
        'quill.snow',
        'components/quill_style'
    ];

    $scripts = ['stepper_form'];

    ?>

    <?php require 'app/views/layouts/header.php' ?>

    <?php require 'app/views/partials/alert.php' ?>
    <?php require 'app/views/partials/icon.php' ?>
    <?php require 'app/views/layouts/teacher_sidebar.php' ?>

    <main class="dashboard-content">
        <div class="dashboard-container dashboard-container--form">

            <a href="<?= BASEURL . '/teacher/course/' . ($course['course_id'] ?? '') ?>" class="back-link page-back-link">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
                Back to Course Materials
            </a>

            <div class="dashboard-topbar">
                <div class="dashboard-greeting">
                    <h1>
                        <?= htmlspecialchars($material['title'] ?? '') ?>
                        <span class="material-status-badge status-<?= htmlspecialchars($material['status'] ?? 'draft') ?>">
                            <?= htmlspecialchars(ucfirst($course['status'] ?? 'draft')) ?>
                        </span>
                    </h1>
                    <p>For course: <strong><?= htmlspecialchars($course['title'] ?? '') ?></strong></p>
                </div>
                <a href="<?= BASEURL . '/edit/text/' . ($material['material_id'] ?? '') ?>" class="btn btn-sm btn-primary">
                    Edit Material
                </a>
            </div>

            <div class="card">

                <div class="form-step" id="formStep1">

                    <div class="form-section">
                        <h2 class="form-section-title">Material Text</h2>

                        <div class="material-content">
                            <?= $material['content'] ?>
                        </div>
                    </div>

                    <div class="form-actions" style="">
                        <a href="<?= BASEURL . '/teacher/course/' . ($course['course_id'] ?? '') ?>" class="btn btn-outline">
                            Back
                        </a>

                        <button
                            type="button"
                            class="btn btn-primary"
                            onclick="goToStep(2)"
                        >
                            Next
                        </button>
                    </div>

                </div>

                <div class="form-step" id="formStep2" hidden>

                    <div class="form-section">
                        <h2 class="form-section-title">Quiz Settings</h2>

                        <div class="quiz-settings-grid">
                            <div class="quiz-settings-item">
                                <div class="value">
                                    <?= htmlspecialchars($settings['minimum_correct'] ?? '-') ?>
                                    /
                                    <?= htmlspecialchars($settings['total_questions'] ?? count($questions ?? [])) ?>
                                </div>
                                <div class="label">Minimum Correct</div>
                            </div>

                            <div class="quiz-settings-item">
                                <div class="value">
                                    <?= htmlspecialchars($settings['max_attempts'] ?? '-') ?>
                                </div>
                                <div class="label">Max Attempts</div>
                            </div>

                            <div class="quiz-settings-item">
                                <div class="value">
                                    <?= htmlspecialchars($settings['timer'] ?? '-') ?> min
                                </div>
                                <div class="label">Timer</div>
                            </div>

                            <div class="quiz-settings-item">
                                <div class="value">
                                    <?= htmlspecialchars($settings['reset_minutes'] ?? '-') ?> min
                                </div>
                                <div class="label">Reset After</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="form-section-title">Quiz Questions</h2>

                        <?php if (!empty($questions)) : ?>

                            <?php foreach ($questions as $i => $q) : ?>

                                <div class="quiz-view-question">

                                    <div class="quiz-view-question-title">
                                        Question <?= $i ?>:
                                        <?= htmlspecialchars($q['question'] ?? '') ?>
                                    </div>

                                    <div class="quiz-view-options">

                                        <?php foreach ($q['options'] as $optionNumber => $optionText) : ?>

                                            <?php
                                                $isCorrect = (int) ($q['correct'] ?? 0) === (int) $optionNumber;
                                                $letter = chr(64 + (int) $optionNumber);
                                            ?>

                                            <div class="quiz-view-option <?= $isCorrect ? 'correct' : '' ?>">

                                                <span class="option-letter">
                                                    <?= $letter ?>
                                                </span>

                                                <span>
                                                    <?= htmlspecialchars($optionText ?? '') ?>
                                                </span>

                                                <?php if ($isCorrect) : ?>
                                                    <svg class="icon" aria-hidden="true">
                                                        <use href="#i-check"></use>
                                                    </svg>
                                                <?php endif; ?>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        <?php else : ?>

                            <p class="form-hint">
                                No quiz questions have been added yet.
                            </p>

                        <?php endif; ?>

                    </div>

                    <div class="form-actions" style="justify-content: flex-start;">
                        <button
                            type="button"
                            class="btn btn-outline"
                            onclick="goToStep(1)"
                        >
                            Back
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <?php require 'app/views/layouts/footer.php' ?>