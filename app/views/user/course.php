<?php

$title = 'Explore Course';

$styles = ['components/navbar', 'pages/user/course', 'components/button'];

$scripts = ['user_open'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?>

<div class="container">

    <!-- COURSE HERO -->

    <div class="course-hero">

        <span class="course-category">
            <?= $course['category_name'] ?> • <?= $course['difficulty'] ?>
        </span>

        <h1 class="course-title">
            <?= $course['course_name'] ?>
        </h1>

        <p class="course-description">
            <?= $course['description'] ?>
        </p>

        <div class="course-meta">
            <span><?= $course['total_materials'] ?> Materials</span>
            <span><?= numberShort($course['total_students']) ?> Students</span>
            <span><?= $course['average_rating'] ?> Rating</span>
            <span><?= timeAgo($course['created_at']) ?></span>
        </div>

        <?php if($isEnroll) : ?>

            <a href="<?= BASEURL ?>/course/resume/<?= $course['course_id'] ?>" class="btn btn-green">
                Continue Learning
            </a>
            
        <?php else : ?>

            <form action="<?= BASEURL ?>/course/enroll" method="post">

                <?= csrf() ?>

                <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">

                <button type="submit" class="btn btn-primary">
                    Start Learning
                </button>
                
            </form>

        <?php endif; ?>

        

    </div>

    <!-- INSTRUCTOR -->

    <div class="section">

        <div class="section-header">
            <h2 class="section-title">Instructor</h2>
        </div>

        <div class="instructor">

            <div class="avatar">
                JD
            </div>

            <div>

                <div class="instructor-name">
                    <?= $course['teacher_name'] ?>
                </div>

                <div class="instructor-role">
                    <?= $course['job_title'] ?>
                </div>

            </div>

        </div>

    </div>

    <!-- COURSE CONTENT -->

    <div class="section">

        <div class="section-header">

            <h2 class="section-title">
                Course Content
            </h2>

            <span class="section-count">
                <?= $course['total_materials'] ?> Materials
            </span>

        </div>

        <div class="material-list">

            <?php if(!empty($materials)) : ?>

                <?php foreach($materials as $material) : ?>

                    <a href="<?= BASEURL ?>/material/<?= $material['material_id'] ?>" class="material">

                        <div class="material-number"><?= $material['order_index'] ?></div>

                        <div class="material-content">

                            <div class="material-title">
                                <?= $material['title'] ?>
                            </div>

                            <div class="material-description">
                                
                            </div>

                        </div>

                        <div class="material-type">
                            <?= $material['type'] ?>
                        </div>

                        <div class="material-arrow">
                            →
                        </div>

                    </a>
                    
                <?php endforeach; ?>
                
            <?php endif; ?>

            

        </div>

    </div>

</div>

<?php require 'app/views/layouts/footer.php' ?>