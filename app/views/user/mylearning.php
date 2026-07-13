<?php
    
$title = 'My Learning';

$styles = ['pages/user/mylearning', 'components/navbar', 'components/button', 'components/header'];

$scripts = ['user_open'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?>

<div class="container">

    <!-- HEADER -->

    <div class="header">

        <div class="header-top">

            <div>

                <span class="header-badge">Student Dashboard</span>
    
                <h1 class="header-title">My Learning</h1>
        
                <p class="header-text">
                    Track your progress, continue learning, and manage your enrolled courses in one place.
                </p>

            </div> 

        </div>
        
        <!-- STATS -->

        <div class="stats">

            <div class="stat">
                <h3><?= $stats['enrolled_courses'] ?></h3>
                <p>Enrolled Courses</p>
            </div>

            <div class="stat">
                <h3><?= $stats['completed_courses'] ?></h3>
                <p>Completed Courses</p>
            </div>

            <div class="stat">
                <h3><?= $stats['avg_progress'] ?? 0 ?>%</h3>
                <p>Avg Progress</p>
            </div>

            <div class="stat">
                <h3><?= $stats['completed_materials'] ?></h3>
                <p>Total Completed Material</p>
            </div>

        </div>

        <div class="header-search">
            <input type="text" placeholder="Search your courses...">
        </div>

    </div>

    <!-- FILTER -->

    <div class="course-filter-section">

        <div class="course-filter-wrapper">

            <div class="course-filter">

                <button class="filter-btn active">
                    All Courses
                </button>

                <button class="filter-btn">
                    In Progress
                </button>

                <button class="filter-btn">
                    Completed
                </button>

            </div>

        </div>

    </div>

    <h2 class="section-title">My Courses</h2>

    <!-- GRID -->

    <div class="grid">

        <!-- CONTINUE LEARNING -->

        <?php if ($lastOpenedCourse) : ?>

            <div class="continue">

                <div class="continue-thumb"></div>

                <div class="continue-content">

                    <span class="continue-label">Continue Learning</span>

                    <h2 class="continue-title"><?= $lastOpenedCourse['course_name'] ?></h2>

                    <p class="continue-sub">
                        Last lesson: <?= $lastOpenedCourse['title'] ?>
                    </p>

                    <div class="progress-top">
                        <span><?= $lastOpenedCourse['total_completed'] ?> / <?= $lastOpenedCourse['total_materials'] ?> Lessons</span>
                        <span><?= $lastOpenedCourse['avg_progress'] ?>%</span>
                    </div>

                    <div class="progress">
                        <div class="progress-fill" style="width: <?= $lastOpenedCourse['avg_progress'] ?>;"></div>
                    </div>

                    <div class="meta">
                        <span>📚 <?= $lastOpenedCourse['total_materials'] ?> Materials</span>
                        <span>⏱ 8 Hours</span>
                        <span>👨‍🏫 <?= $lastOpenedCourse['teacher_name'] ?></span>
                    </div>

                    <a href="<?= BASEURL . '/course/' . $lastOpenedCourse['course_id'] ?>" class="btn btn-primary">Continue Course</a>

                </div>

            </div>

            <?php if ($userCourses) : ?>

                <?php foreach ($userCourses as $course) : ?>

                    <div class="card-course">

                        <div class="thumb"></div>

                        <div class="content">

                            <span class="cat"><?= $course['category_name'] ?></span>

                            <h3 class="course-title"><?= $course['course_name'] ?></h3>

                            <p class="text"><?= $course['short_description'] ?></p>

                            <div class="small-progress">

                                <div class="small-progress-top">
                                    <span>Progress</span>
                                    <span><?= $course['avg_progress'] ?>%</span>
                                </div>

                                <div class="progress">
                                    <div class="progress-fill" style="width:<?= $course['avg_progress'] ?>%"></div>
                                </div>

                            </div>

                            <div class="card-footer">

                                <span class="teacher"><?= $course['teacher_name'] ?></span>
                                <a class="link" href="<?= BASEURL . '/course/' . $course['course_id'] ?>"><?= $course['is_completed'] ? 'Review →' : 'Continue →'?></a>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        <?php else : ?>

            <div class="continue-content empty-learning">

                <div class="empty-box">

                    <h2 class="continue-title">
                        No Learning Activity Yet
                    </h2>

                    <p class="continue-sub">
                        You haven't enrolled in any courses yet.
                        Explore available courses and start learning today.
                    </p>

                    <a
                        href="<?= BASEURL . '/courses' ?>"
                        class="btn btn-primary"
                    >
                        Browse Courses
                    </a>

                </div>

            </div>

        <?php endif; ?>

        

    </div>

</div>

<?php require 'app/views/layouts/footer.php' ?>
