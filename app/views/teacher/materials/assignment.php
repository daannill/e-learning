<?php

$title = ($isEdit ? 'Edit' : 'Add') . ' Text Material';

$styles = [
    'components/button',
    'components/form',
    'components/teacher_sidebar',
    'components/teacher_layout',
    'quill.snow',
    'components/quill_style'
];

$scripts = ['quill.min', 'quill_setup'];
?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/partials/alert.php' ?>
<?php require 'app/views/partials/icon.php' ?>
<?php require 'app/views/layouts/teacher_sidebar.php' ?>

<main class="dashboard-content">
    <div class="dashboard-container dashboard-container--form">

        <a href="<?= BASEURL . '/teacher/course/' . $course['course_id'] ?>" class="back-link page-back-link">
            <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
            Back to Course Materials
        </a>

        <div class="dashboard-topbar">
            <div class="dashboard-greeting">
                <h1><?= $isEdit ? 'Edit' : 'Add' ?> Assignment</h1>
                <p>For course: <strong><?= $course['title'] ?></strong></p>
            </div>
        </div>

        <form action="<?= BASEURL . $formAction ?>" method="post" class="card" novalidate>
            <?= csrf() ?>

            <!-- ===== STEP 1: MATERIAL DETAILS ===== -->
            <div class="form-step">

                <div class="form-section">
                    <h2 class="form-section-title">Assignment Details</h2>

                    <div class="form-group">
                        <label class="form-label" for="material_title">Assignment Title</label>
                        <input class="form-control <?= invalid('title') ?>" type="text" id="material_title" name="title" value="<?= htmlspecialchars(old('title') ?: ($material['title'] ?? '')) ?>" placeholder="e.g. Introduction to Variables" maxlength="120" required>
                        <?= error('title') ?>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="passing_score">Passing Score</label>

                            <input
                                class="form-control <?= invalid('passing_score') ?>"
                                type="number"
                                id="passing_score"
                                name="passing_score"
                                value="<?= htmlspecialchars(old('passing_score') ?: ($material['passing_score'] ?? '')) ?>"
                                min="0"
                                max="100"
                                required
                            >

                            <?php if (hasFlash('errors.passing_score')): ?>
                                <?= error('passing_score') ?>
                            <?php else: ?>
                                <p class="form-hint">
                                    Minimum score students must achieve.
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="deadline_at">Submission Duration (Hours)</label>

                            <input
                                class="form-control <?= invalid('deadline_at') ?>"
                                type="number"
                                id="deadline_at"
                                name="deadline_at"
                                value="<?= old('deadline_at') ?: ($material['deadline_at'] ?? '') ?>"
                                min="1"
                                required
                            >

                            <?php if (hasFlash('errors.deadline_at')): ?>
                                <?= error('deadline_at') ?>
                            <?php else: ?>
                                <p class="form-hint">
                                    Students must submit within this many hours after opening the assignment.
                                </p>
                            <?php endif; ?>
                        </div>

                    </div>

                    <h2 class="form-section-title">Content Details</h2>

                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="editor">Content Text</label>

                        <?php include APP_PATH. '/app/views/partials/form_text.php'; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <p class="form-hint form-actions-note">Next, you'll add quiz questions for this lesson.</p>
                    <a href="<?= BASEURL . '/teacher/course/' . $course['course_id'] ?>" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Assignment</button>
                </div>

            </div>

    </div>
</main>

<?php require 'app/views/layouts/footer.php' ?>