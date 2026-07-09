<?php
    
$title = 'Video';

$styles = ['pages/user/video', 'components/navbar', 'components/button', 'components/material_sidebar'];

$scripts = ['user_open', 'material_sidebar_open'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?> 

<div class="learning-layout">

    <?php require 'app/views/layouts/material_sidebar.php' ?> 

    <!-- CONTENT -->

    <main class="content">

        <?php if ($materialContent['source_type'] === 'video') : ?>

            <div class="video-wrapper">

                <video
                    class="video-player"
                    controls
                    controlsList="nodownload"
                >

                    <source
                        src="<?= $materialContent['video_url'] ?>"
                        type="video/mp4"
                    >

                </video>

            </div>

        <?php elseif ($materialContent['source_type'] === 'youtube') : ?>

            <div class="video-wrapper">

                <iframe
                    class="video-player"
                    src="<?= $materialContent['video_url'] ?>"
                    allowfullscreen>
                </iframe>

            </div>

        <?php endif; ?>

        <div class="material-info">

            <h1 class="material-title-main">
                <?= htmlspecialchars($materialInfo['title']) ?>
            </h1>

            <!-- <div class="material-description">

            </div> -->

            <div class="material-meta">

                <span>
                    <?= ucfirst($materialInfo['type']) ?> Material
                </span>

                <span>
                    Lesson <?= $materialInfo['order_index'] ?> of 24
                </span>

                <!-- <span>
                    Intermediate
                </span> -->

            </div>

            <div class="lesson-actions">

                <?php if ($prevMaterial) : ?>

                    <a href="<?= BASEURL . '/material/' . $prevMaterial ?>" class="btn btn-secondary">
                        ← Previous Lesson
                    </a>

                <?php endif; ?>

                <a href="<?= BASEURL . '/material/' . $materialInfo['material_id'] . '/quiz' ?>" class="btn btn-primary next-btn">
                    Quiz →
                </a>

            </div>

        </div>

    </main>

</div>

<?php require 'app/views/layouts/footer.php' ?> 