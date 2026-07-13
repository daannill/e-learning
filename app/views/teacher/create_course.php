<?php

$title = 'Add New Course';

$styles = [
    'components/button',
    'components/form',
    'components/teacher_sidebar',
    'components/teacher_layout',
];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/partials/alert.php' ?>
<?php require 'app/views/partials/icon.php' ?>
<?php require 'app/views/layouts/teacher_sidebar.php' ?>

<main class="dashboard-content">
    <div class="dashboard-container dashboard-container--form">

        <a href="#" class="back-link page-back-link">
            <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
            Back to My Courses
        </a>

        <div class="dashboard-topbar">
            <div class="dashboard-greeting">
                <h1>Add New Course</h1>
                <p>Fill in the details below to create a new course.</p>
            </div>
        </div>

        <form class="card" novalidate>

            <div class="form-section">
                <h2 class="form-section-title">Thumbnail</h2>
                <label class="upload-box" for="course_thumbnail">
                    <svg class="icon" aria-hidden="true"><use href="#i-upload"></use></svg>
                    <span class="upload-title">Click to upload thumbnail</span>
                    <span class="upload-hint">PNG or JPG, up to 2MB — recommended ratio 16:9</span>
                    <input type="file" id="course_thumbnail" name="thumbnail" accept="image/png, image/jpeg" hidden>
                </label>
            </div>

            <div class="form-section">
                <h2 class="form-section-title">Basic Information</h2>

                <div class="form-group">
                    <label class="form-label" for="course_title">Course Title</label>
                    <input class="form-control" type="text" id="course_title" name="title" placeholder="e.g. Learn PHP from Scratch" maxlength="120" required>
                    <!--
                        Contoh error state (tambahin manual kalau field ini invalid):
                        <input class="form-control error" ...>
                        <span class="form-error">Course title is required.</span>
                    -->
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="course_category">Category</label>
                        <select class="form-control" id="course_category" name="category" required>
                            <option value="" disabled selected>Select a category</option>
                            <option value="web-development">Web Development</option>
                            <option value="programming">Programming</option>
                            <option value="computer-science">Computer Science</option>
                            <option value="design">Design</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="course_level">Level</label>
                        <select class="form-control" id="course_level" name="level" required>
                            <option value="" disabled selected>Select a level</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="course_short_description">Short Description</label>
                    <textarea class="form-control" id="course_short_description" name="short_description" rows="3" maxlength="200" placeholder="A one or two sentence summary that will appear on the course card..." required></textarea>
                    <p class="form-hint">Max 200 characters. This shows up on the course card — keep it short and catchy.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="course_description">Description</label>
                    <textarea class="form-control" id="course_description" name="description" rows="8" maxlength="2000" placeholder="Describe what students will learn, how the course is structured, prerequisites, and who this course is for..." required></textarea>
                    <p class="form-hint">Full description shown on the course detail page. Max 2000 characters.</p>
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