<?php

    $title = 'Register';
    $styles = ['components/button', 'components/form', 'pages/auth/auth'];
    $scripts = ['avatar_preview']

?>

<?php require 'app/views/layouts/header.php' ?>

<a href="<?= BASEURL ?>/" class="back-btn" onclick="history.back(); return false;">
    ← Back
</a>

<div class="auth">

    <div class="auth-card-register card">

        <div class="auth-header">

            <h1>
                Create Account
            </h1>

            <p>
                Fill in your information to get started
            </p>

        </div>

        <?php if(hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= flash('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASEURL ?>/register" method="POST" enctype="multipart/form-data">

            <?= csrf() ?>

            <div class="avatar-upload">

                <label for="avatarInput" class="avatar-upload-frame <?= invalid('avatar') ?>" id="avatarFrame">

                    <img
                        id="avatarPreview"
                        src=""
                        alt=""
                        style="display:none;"
                    >

                    <svg class="icon avatar-upload-placeholder" id="avatarPlaceholder" aria-hidden="true">
                        <use href="#i-upload"></use>
                    </svg>

                </label>

                <label for="avatarInput" class="avatar-upload-label">
                    Upload profile picture
                </label>

                <input
                    type="file"
                    name="avatar"
                    id="avatarInput"
                    accept="image/*"
                    class="avatar-upload-input"
                    onchange="previewAvatar(this)"
                >

                <?= error('avatar') ?>

            </div>

            <div class="form-grid">

                <div class="form-group">

                    <label class="form-label">
                        First Name
                    </label>

                    <input
                        name="fname"
                        type="text"
                        class="form-control <?= invalid('fname') ?>"
                        placeholder="First name"
                        value="<?= old('fname') ?>"
                    >
                    
                    <?= error('fname') ?>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Last Name
                    </label>

                    <input
                        name="lname"
                        type="text"
                        class="form-control <?= invalid('lname') ?>"
                        placeholder="Last name"
                        value="<?= old('lname') ?>"
                    >

                    <?= error('lname') ?>

                </div>

            </div>

            <div class="form-grid">

                <div class="form-group">

                    <label class="form-label">
                        Gender
                    </label>

                    <select name="gender" class="form-control <?= invalid('gender') ?>">

                        <option value="">
                            Select gender
                        </option>

                        <option value="male" <?= old('gender') === "male" ? 'selected': '' ?>>
                            Male
                        </option>

                        <option value="female" <?= old('gender') === "female" ? 'selected': '' ?>>
                            Female
                        </option>

                    </select>

                    <?= error('gender') ?>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        name="email"
                        type="email"
                        class="form-control <?= invalid('email') ?>"
                        placeholder="Enter email"
                        value="<?= old('email') ?>"
                    >

                    <?= error('email') ?>

                </div>

            </div>

            <div class="form-group">

                <label class="form-label">
                    Address
                </label>

                <textarea
                    name="address"
                    class="form-control <?= invalid('address') ?>"
                    rows="3"
                    placeholder="Enter your address"
                ><?= old('address') ?></textarea>

                <?= error('address') ?>

            </div>

            <div class="form-grid">

                <div class="form-group">

                    <label class="form-label">
                        Password
                    </label>

                    <input
                        name="password1"
                        type="password"
                        class="form-control <?= invalid('password1') ?>"
                        placeholder="Create password"
                    >

                    <?= error('password1') ?>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Confirm Password
                    </label>

                    <input
                        name="password2"
                        type="password"
                        class="form-control <?= invalid('password2') ?>"
                        placeholder="Confirm password"
                    >

                    <?= error('password2') ?>

                </div>

            </div>

            <button class="btn btn-primary w-full">
                Create Account
            </button>

        </form>

        <div class="auth-footer">

            <p>
                Already have an account?
                <a href="<?= BASEURL ?>/login" class="auth-link">
                    Login
                </a>
            </p>

            <div class="auth-divider">
                <span>
                    OR
                </span>
            </div>

            <div class="teacher-box">

                <div class="teacher-content">

                    <h3>
                        Want to become a teacher?
                    </h3>

                    <p>
                        Create courses, manage students, and share your knowledge.
                    </p>

                </div>

                <button class="btn btn-outline w-full">
                    Register as Teacher
                </button>

            </div>

        </div>

    </div>

</div>

<?php require 'app/views/layouts/footer.php' ?>