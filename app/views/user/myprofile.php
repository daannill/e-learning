<?php

/** 
 * @var array $user 
 * 
*/
    
$title = 'My Learning';

$styles = ['pages/user/myprofile', 'components/navbar', 'components/button', 'components/header_home'];

$scripts = ['user_open'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?>


<div class="container">

    <div class="profile-layout">

        <!-- LEFT -->

        <div>

            <div class="profile-card">

                <div class="profile-avatar">
                    DA
                </div>

                <h2 class="profile-name">
                    <?= $user['full_name'] ?>
                </h2>

                <p class="profile-role">
                    <?= ucfirst(authInfo('role')) ?>
                </p>

                <p class="profile-email">
                    <?= $user['email'] ?>
                </p>

                <a href="#" class="btn btn-primary">
                    Edit Profile
                </a>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="content">

            <!-- STATS -->

            <div class="stats-grid">

                <div class="stat-card">
                    <h3><?= $stats['enrolled_courses'] ?></h3>
                    <p>Courses Enrolled</p>
                </div>

                <div class="stat-card">
                    <h3><?= $stats['completed_courses'] ?></h3> 
                    <p>Completed Courses</p>
                </div>

                <div class="stat-card">
                    <h3><?= $stats['completed_materials'] ?></h3>
                    <p>Completed Materials</p>
                </div>

                <div class="stat-card">
                    <h3><?= $stats['in_progress_courses'] ?></h3>
                    <p>In Progress Course</p>
                </div>

            </div>

            <!-- ACCOUNT -->

            <div class="card">

                <h3 class="card-title">
                    Account Information
                </h3>

                <div class="info-grid">

                    <div class="info-item">
                        <label>Full Name</label>
                        <span><?= $user['full_name'] ?></span>
                    </div>

                    <div class="info-item">
                        <label>Email</label>
                        <span><?= $user['email'] ?></span>
                    </div>

                    <div class="info-item">
                        <label>Address</label>
                        <span><?= $user['address'] ?></span>
                    </div>

                    <div class="info-item">
                        <label>Member Since</label>
                        <span><?= $user['join_since']  ?></span>
                    </div>

                </div>

            </div>

            <!-- PROGRESS -->

            <div class="card">

                <h3 class="card-title">
                    Learning Progress
                </h3>

                <div class="progress-row">

                    <span>Overall Progress</span>
                    <span><?= $stats['avg_progress'] ?>%</span>

                </div>

                <div class="progress-bar">

                    <div class="progress-fill" style="width: <?= $stats['avg_progress'] ?>;"></div>

                </div>

            </div>

            <!-- ACTIVITY -->

            <div class="card">

                <h3 class="card-title">
                    Recent Activity
                </h3>

                <div class="activity-list">

                    <?php foreach ($recentActivity as $item) : ?>
                        <div class="activity-item">

                            <div class="activity-info">

                                <h4>
                                    <?= $item['course_name'] ?>
                                </h4>

                                <p>
                                    <?= $item['title'] ?>
                                </p>

                            </div>

                            <span class="activity-status">
                                <?= timeAgo($item['opened_at']) ?>
                            </span>

                        </div>
                    <?php endforeach; ?>    

                </div>

            </div>

        </div>

    </div>
    
</div>


<?php require 'app/views/layouts/footer.php' ?>