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
                <a href="<?= BASEURL . '/edit/assignment/' . ($material['material_id'] ?? '') ?>" class="btn btn-sm btn-primary">
                    Edit Assignment
                </a>
            </div>

            <div class="card">

                <div class="form-step">
                    <div class="quiz-settings-grid">
                        <div class="quiz-settings-item">
                            <div class="value">
                                <?= htmlspecialchars($material['passing_score'] ?? '-') ?>
                            </div>
                            <div class="label">Passing Score</div>
                        </div>

                        <div class="quiz-settings-item">
                            <div class="value">
                                <?= htmlspecialchars($material['deadline_at'] ?? '-') ?>
                            </div>
                            <div class="label">Submission Duration</div>
                        </div>

                    </div>

                    <div class="form-section">
                        <h2 class="form-section-title">Assignment Description</h2>

                        <div class="material-content">
                            <?= $material['content'] ?>
                        </div>
                    </div>

                    <div class="form-actions" style="">
                        <a href="<?= BASEURL . '/teacher/course/' . ($course['course_id'] ?? '') ?>" class="btn btn-outline">
                            Back
                        </a>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <?php require 'app/views/layouts/footer.php' ?>