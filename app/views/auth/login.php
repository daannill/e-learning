<?php 

    $title = 'Login';

    $styles = ['components/button', 'components/form', 'pages/auth/auth', 'components/alert'];

?>

<?php require 'app/views/layouts/header.php'; ?>

<?= successAlert() ?>

<a href="<?= BASEURL ?>" class="back-btn">
    ← Back to dashboard
</a>

<div class="auth">

    <div class="auth-card-login card">

        <div class="auth-header">

            <h1>
                Welcome Back
            </h1>

            <p>
                Sign in to continue to your account
            </p>

        </div>

        <?php if(hasFlash('error')): ?>
            <div class="alert alert-danger">
                <?= flash('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASEURL ?>/login" method="POST">

            <?= csrf() ?>

            <div class="form-group">

                <label class="form-label">
                    Email
                </label>

                <input
                    name="email"
                    type="email"
                    class="form-control <?= invalid('email') ?>"
                    placeholder="Enter your email"
                    value="<?= old('email') ?>"
                >

                <?= error('email') ?>

            </div>

            <div class="form-group">

                <label class="form-label">
                    Password
                </label>

                <input
                    name="password"
                    type="password"
                    class="form-control <?= invalid('password') ?>"
                    placeholder="Enter your password"
                >

                <?= error('password') ?>

            </div>

            <button class="btn btn-primary w-full">
                Sign In
            </button>

        </form>

        <div class="auth-footer">

            <p>
                Don’t have an account?
                <a href="<?= BASEURL ?>/register" class="auth-link">
                    Register
                </a>
            </p>

        </div>

    </div>

</div>

<?php require 'app/views/layouts/footer.php' ?>