<?php

$title = 'Add New Course';

$styles = [
    'components/button',
    'components/form',
    'components/teacher_sidebar',
    'components/teacher_layout',
];

$scripts = ['preview_img']

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/partials/alert.php' ?>
<?php require 'app/views/partials/icon.php' ?>
<?php require 'app/views/layouts/teacher_sidebar.php' ?>

<main class="dashboard-content">
    <div class="dashboard-container dashboard-container--form">

        <a href="<?= BASEURL . '/teacher/courses' ?>" class="back-link page-back-link">
            <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
            Back to My Courses
        </a>

        <div class="dashboard-topbar">
            <div class="dashboard-greeting">
                <h1>Add New Course</h1>
                <p>Fill in the details below to create a new course.</p>
            </div>
        </div>

        <form action="<?= BASEURL . '/create/course' ?>" method="post" class="card" enctype="multipart/form-data" novalidate>
            <?= csrf() ?>

            <div class="form-section">

                <h2 class="form-section-title">
                    Thumbnail
                </h2>

                <div class="upload-wrapper">

                    <label
                        class="upload-box <?= invalid('thumbnail') ?>"
                        for="course_thumbnail"
                    >

                        <img
                            id="thumbnail-preview"
                            class="upload-preview"
                            src=""
                            alt="Thumbnail Preview"
                            hidden
                        >

                        <div class="upload-placeholder">

                            <svg class="icon" aria-hidden="true">
                                <use href="#i-upload"></use>
                            </svg>

                            <span class="upload-title">
                                Click to upload thumbnail
                            </span>

                            <?php if (hasFlash('errors.thumbnail')) : ?>

                                <?= error('thumbnail') ?>

                            <?php else : ?>

                                <span class="upload-hint">
                                    PNG or JPG, up to 2MB — recommended ratio 16:9
                                </span>

                            <?php endif; ?>

                        </div>

                        <input
                            type="file"
                            id="course_thumbnail"
                            name="thumbnail"
                            accept="image/png, image/jpeg"
                            hidden
                        >

                    </label>

                    <div
                        class="upload-actions"
                        id="upload-actions"
                        hidden
                    >
                        <button
                            type="button"
                            class="btn btn-outline btn-sm"
                            id="change-thumbnail"
                        >
                            Change
                        </button>

                        <button
                            type="button"
                            class="btn btn-danger btn-sm"
                            id="remove-thumbnail"
                        >
                            Remove
                        </button>
                    </div>

                </div>

            </div>

            <div class="form-section">
                <h2 class="form-section-title">Basic Information</h2>

                <div class="form-group">

                    <label class="form-label" for="course_title">
                        Course Title
                    </label>

                    <input
                        class="form-control <?= invalid('title') ? 'error' : '' ?>"
                        type="text"
                        id="course_title"
                        name="title"
                        placeholder="e.g. Learn PHP from Scratch"
                        maxlength="120"
                        value="<?= old('title') ?>"
                        required
                    >

                    <?= error('title') ?>

                </div>

                <div class="form-grid">
                    
                    <div class="form-group">

                        <label class="form-label" for="course_category">
                            Category
                        </label>

                        <select
                            class="form-control <?= invalid('category') ? 'error' : '' ?>"
                            id="course_category"
                            name="category"
                            required
                        >

                            <option
                                value=""
                                disabled
                                <?= empty(old('category')) ? 'selected' : '' ?>
                            >
                                Select a category
                            </option>

                            <?php foreach ($categories as $category) : ?>

                                <option
                                    value="<?= $category['category_id'] ?>"
                                    <?= old('category') === $category['category_id'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <?= error('category') ?>

                    </div>

                    <div class="form-group">

                        <label class="form-label" for="course_level">
                            Difficulty
                        </label>

                        <select
                            class="form-control <?= invalid('difficulty') ? 'error' : '' ?>"
                            id="course_level"
                            name="difficulty"
                            required
                        >

                            <option
                                value=""
                                disabled
                                <?= empty(old('difficulty')) ? 'selected' : '' ?>
                            >
                                Select a difficulty
                            </option>

                            <option
                                value="beginner"
                                <?= old('difficulty') === 'beginner' ? 'selected' : '' ?>
                            >
                                Beginner
                            </option>

                            <option
                                value="intermediate"
                                <?= old('difficulty') === 'intermediate' ? 'selected' : '' ?>
                            >
                                Intermediate
                            </option>

                            <option
                                value="advanced"
                                <?= old('difficulty') === 'advanced' ? 'selected' : '' ?>
                            >
                                Advanced
                            </option>

                        </select>

                        <?= error('difficulty') ?>

                    </div>

                </div>

                <div class="form-group">

                    <label class="form-label" for="course_short_description">
                        Short Description
                    </label>

                    <textarea
                        class="form-control <?= invalid('short_description') ? 'error' : '' ?>"
                        id="course_short_description"
                        name="short_description"
                        rows="3"
                        maxlength="200"
                        placeholder="A one or two sentence summary that will appear on the course card..."
                        required
                    ><?= old('short_description') ?></textarea>

                    <?php if (hasFlash('errors.short_description')) : ?>

                        <?= error('short_description') ?>

                    <?php else : ?>

                        <p class="form-hint">
                            Max 200 characters. This shows up on the course card — keep it short and catchy.
                        </p>

                    <?php endif; ?>

                </div>

                <div class="form-group">

                    <label class="form-label" for="course_description">
                        Description
                    </label>

                    <textarea
                        class="form-control <?= invalid('description') ? 'error' : '' ?>"
                        id="course_description"
                        name="description"
                        rows="8"
                        maxlength="2000"
                        placeholder="Describe what students will learn, how the course is structured, prerequisites, and who this course is for..."
                        required
                    ><?= old('description') ?></textarea>

                    <?php if (hasFlash('errors.description')) : ?>

                        <?= error('description') ?>

                    <?php else : ?>

                        <p class="form-hint">
                            Full description shown on the course detail page. Max 2000 characters.
                        </p>

                    <?php endif; ?>

                </div>
            </div>

            <div class="form-actions">
                <p class="form-hint form-actions-note">Course will be saved as a draft — you can publish it after adding materials.</p>
                <a href="#" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Course</button>
            </div>

        </form>

    </div>
</main>

<?php require 'app/views/layouts/footer.php' ?>