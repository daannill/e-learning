<?php
    
$title = 'Text';

$styles = ['pages/user/text', 'components/navbar', 'components/button', 'components/material_sidebar', 'quill.snow'];

$scripts = ['user_open', 'material_sidebar_open', 'quiz_logic', 'quill.min'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?> 

<div class="learning-layout">

    <?php require 'app/views/layouts/material_sidebar.php' ?> 

    <!-- CONTENT -->

    <main class="content">

        <div class="text-material">

            <div class="material-header">

                <span class="material-badge">
                    📄 Text Material
                </span>

                <h1 class="material-title">
                    <?= $materialInfo['title'] ?>
                </h1>

                <p class="material-description">
                    Learn the fundamentals of the Model View Controller pattern
                    and understand how modern web applications are structured.
                </p>

            </div>

            <article class="material-body ql-editor">
                <?= $materialContent['content'] ?>
            </article>

            <div class="lesson-actions">

                <?php if ($prevMaterial) : ?>

                    <a href="<?= BASEURL . '/material/' . $prevMaterial ?>" class="btn btn-secondary">
                        ← Previous Lesson
                    </a>

                <?php endif; ?>

                <a href="<?= BASEURL . '/material/' . $currentMaterial . '/quiz' ?>" class="btn btn-primary">
                    Quiz →
                </a>

            </div>

        </div>

    </main>

</div>

<?php require 'app/views/layouts/footer.php' ?> 