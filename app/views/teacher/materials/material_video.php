<?php

$title = 'Add Video Material';

$styles = [
    'components/button',
    'components/form',
    'components/teacher_sidebar',
    'components/teacher_layout',
];

$scripts = ['preview_video', 'stepper_form', 'quiz_add'];

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
                <h1><?= $isEdit ? 'Edit' : 'Add' ?> Video Material</h1>
                <p>For course: <strong><?= $course['title'] ?></strong></p>
            </div>
        </div>

        <form action="<?= BASEURL . $formAction ?>" method="post" enctype="multipart/form-data" class="card" novalidate>
            <?= csrf() ?>

            <div class="stepper">
                <div class="stepper-step active" data-step="1">
                    <span class="stepper-step-number">1</span>
                    <span class="stepper-step-label">Material Details</span>
                </div>
                <div class="stepper-line"></div>
                <div class="stepper-step" data-step="2">
                    <span class="stepper-step-number">2</span>
                    <span class="stepper-step-label">Quiz</span>
                </div>
                <div class="stepper-line"></div>
                <div class="stepper-step" data-step="3">
                    <span class="stepper-step-number">3</span>
                    <span class="stepper-step-label">Quiz Settings</span>
                </div>
            </div>

            <!-- ===== STEP 1: MATERIAL DETAILS ===== -->
            <div class="form-step" id="formStep1">

                <?php
                    $currentSource = old('video_source') ?: ($material['video_source'] ?? 'youtube');
                    $existingVideoUrl  = $material['video_path'] ?? null;
                ?>

                <div class="form-section">
                    <h2 class="form-section-title">Video Details</h2>

                    <div class="form-group">
                        <label class="form-label" for="material_title">Material Title</label>
                        <input class="form-control <?= invalid('title') ?>" type="text" id="material_title" name="title" value="<?= old('title') ?: ($material['title'] ?? '') ?>" placeholder="e.g. Introduction to Variables" maxlength="120" required>
                        <?= error('title') ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Video Source</label>

                        <div class="source-toggle">
                            <button type="button" class="source-toggle-btn <?= $currentSource === 'youtube' ? 'active' : '' ?>" data-source="youtube" onclick="switchVideoSource('youtube')">
                                <svg class="icon" aria-hidden="true"><use href="#i-video"></use></svg>
                                Video URL
                            </button>
                            <button type="button" class="source-toggle-btn <?= $currentSource === 'video' ? 'active' : '' ?>" data-source="video" onclick="switchVideoSource('video')">
                                <svg class="icon" aria-hidden="true"><use href="#i-upload"></use></svg>
                                Upload Video
                            </button>
                        </div>

                        <input type="hidden" id="video_source" name="video_source" value="<?= $currentSource ?>">

                        <div id="sourcePanelUrl" <?= $currentSource !== 'youtube' ? 'hidden' : '' ?>>
                            <input class="form-control <?= invalid('video_url') ?>" type="url" value="<?= old('video_url') ?: ($material['video_url'] ?? '') ?>" id="material_video_url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
                            <?php if (hasFlash('errors.video_url')) : ?>

                                <?= error('video_url') ?>

                            <?php else : ?>

                                <p class="form-hint">
                                    Paste a YouTube or Vimeo link. Students will watch this video within the lesson.
                                </p>

                            <?php endif; ?>

                            <div class="video-preview-box <?= invalid('video_url') ?>" id="youtubePreview">
                                <svg class="icon" aria-hidden="true"><use href="#i-video"></use></svg>
                                <span class="video-preview-title">Video preview</span>
                                <span class="video-preview-hint">Preview will appear here once a valid link is added.</span>
                            </div>
                        </div>

                        <div id="sourcePanelUpload" <?= $currentSource !== 'video' ? 'hidden' : '' ?>>
                            <div class="upload-box <?= invalid('video_file') ?>" role="button" tabindex="0" aria-label="Upload video file" onclick="triggerUploadInput(this)" onkeydown="handleUploadBoxKeydown(event, this)">
                                <input type="file" id="material_video_file" name="video_file" accept="video/mp4, video/webm" hidden onchange="handleVideoFileChange(this)">

                                <div class="upload-placeholder <?= invalid('video_file') ?>" <?= $existingVideoUrl ? 'hidden' : '' ?>>
                                    <svg class="icon" aria-hidden="true"><use href="#i-upload"></use></svg>
                                    <span class="upload-title">Click to upload video</span>
                                    <span class="upload-hint">MP4 or WebM, up to 200MB</span>
                                </div>

                                <div class="upload-result" <?= !$existingVideoUrl ? 'hidden' : '' ?>>
                                    <video
                                        id="videoPreview"
                                        class="upload-preview"
                                        controls
                                        playsinline
                                        srcsrc="<?= $existingVideoUrl ? htmlspecialchars(APP_PATH . '/uploads/material-videos/' . $existingVideoUrl) : '' ?>""
                                    ></video>

                                    <div class="upload-result--file">
                                        <div class="upload-file-info">
                                            <svg class="icon" aria-hidden="true"><use href="#i-video"></use></svg>
                                            <span id="videoFileName"><?= htmlspecialchars($material['video_filename'] ?? 'filename.mp4') ?></span>
                                        </div>
                                        <button type="button" class="upload-remove" aria-label="Remove file" onclick="removeVideoFile(event)">
                                            <svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php if (hasFlash('errors.video_file')) : ?>

                                <?= error('video_file') ?>

                            <?php else : ?>

                                <p class="form-hint">Uploaded videos are hosted directly on this platform.</p>

                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- <div class="form-section">
                    <h2 class="form-section-title">Additional Information</h2>

                    <div class="form-group">
                        <label class="form-label" for="material_duration">Duration</label>
                        <input class="form-control" type="text" id="material_duration" name="duration" placeholder="e.g. 12:45">
                        <p class="form-hint">Approximate length of the video (optional).</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="material_notes">Notes</label>
                        <textarea class="form-control" id="material_notes" name="notes" rows="4" maxlength="500" placeholder="Any additional notes or context for students about this video..."></textarea>
                    </div>
                </div> -->

                <div class="form-actions">
                    <p class="form-hint form-actions-note">Next, you'll add quiz questions for this lesson.</p>
                    <a href="#" class="btn btn-outline">Cancel</a>
                    <button type="button" class="btn btn-primary" onclick="goToStep(2)">Next: Quiz</button>
                </div>

            </div>

            <!-- ===== STEP 2: QUIZ ===== -->
            <div class="form-step" id="formStep2" hidden>

                <div class="form-section">
                    <h2 class="form-section-title">Quiz Questions</h2>

                    <?php if(hasFlash('errors.quizerr')): ?>
                        <div class="alert alert-danger">
                            <?= flash('errors.quizerr') ?>
                        </div>
                    <?php endif; ?>

                    <?php
                        $oldQuestions = old('quiz') ?: [1 => [], 2 => [], 3 => [], 4 => [], 5 => []];
                        include APP_PATH. '/app/views/partials/quiz_question_list.php';
                    ?>
                </div>

                <div class="form-actions">
                    <p class="form-hint form-actions-note">This material and quiz will be saved as a draft lesson.</p>
                    <button type="button" class="btn btn-outline" onclick="goToStep(1)">Back</button>
                    <button type="button" class="btn btn-primary" onclick="goToStep(3)">Next: Quiz Settings</button>
                </div>
            </div>

            <!-- ===== STEP 3: QUIZ SETTINGS ===== -->
            <div class="form-step" id="formStep3" hidden>

                <div class="form-section">
                    <h2 class="form-section-title">Quiz Settings</h2>

                    <?php 
                        $settings = 
                        include APP_PATH . '/app/views/partials/quiz_settings.php' 
                    ?>
                </div>

                <div class="form-actions">
                    <p class="form-hint form-actions-note">This material and quiz will be saved as a draft lesson.</p>
                    <button type="button" class="btn btn-outline" onclick="goToStep(2)">Back</button>
                    <button type="submit" class="btn btn-primary">Save Material &amp; Quiz</button>
                </div>

            </div>

            
        </form>

        <!-- <template id="quizQuestionTemplate">
            <div class="quiz-question" data-question-index="__INDEX__">
                <div class="quiz-question-header">
                    <span class="quiz-question-number">Question</span>
                    <button type="button" class="quiz-remove-btn" onclick="removeQuizQuestion(this)" aria-label="Remove question">
                        <svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg>
                    </button>
                </div>
                <div class="form-group">
                    <label class="form-label">Question</label>
                    <textarea class="form-control" name="quiz[__INDEX__][question]" rows="2" placeholder="e.g. What does PHP stand for?" required></textarea>
                </div>
                <div class="quiz-options">
                    <div class="quiz-option">
                        <input type="radio" name="quiz[__INDEX__][correct]" value="1" checked>
                        <input class="form-control" type="text" name="quiz[__INDEX__][options][1]" placeholder="Option A">
                    </div>
                    <div class="quiz-option">
                        <input type="radio" name="quiz[__INDEX__][correct]" value="2">
                        <input class="form-control" type="text" name="quiz[__INDEX__][options][2]" placeholder="Option B">
                    </div>
                    <div class="quiz-option">
                        <input type="radio" name="quiz[__INDEX__][correct]" value="3">
                        <input class="form-control" type="text" name="quiz[__INDEX__][options][3]" placeholder="Option C">
                    </div>
                    <div class="quiz-option">
                        <input type="radio" name="quiz[__INDEX__][correct]" value="4">
                        <input class="form-control" type="text" name="quiz[__INDEX__][options][4]" placeholder="Option D">
                    </div>
                </div>
            </div>
        </template> -->

    </div>
</main>

<?php require 'app/views/layouts/footer.php' ?>