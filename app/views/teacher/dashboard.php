<?php

$title = 'Teacher Dashboard';

$styles = [
    'components/button',
    'components/course_card',
    'components/teacher_sidebar',
    'pages/teacher/teacher_dashboard',
];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/partials/alert.php' ?>
<?php require 'app/views/partials/icon.php' ?>
<?php require 'app/views/layouts/teacher_sidebar.php' ?>

<main class="dashboard-content">
    <div class="dashboard-container">

        <div class="dashboard-topbar">
            <div class="dashboard-greeting">
                <span class="topbar-date">Friday, July 10</span>
                <h1>Hi, <?= authInfo('name') ?> 👋</h1>
                <p>Here's what's happening with your courses today.</p>
            </div>
            <a href="<?= BASEURL . '/teacher/course/create' ?>" class="btn btn-primary">
                <svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg>
                Add Course
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card stat-card--courses">
                <span class="stat-card-icon">
                    <svg class="icon" aria-hidden="true"><use href="#i-book"></use></svg>
                </span>
                <div>
                    <h3><?= $teacherStats['total_courses'] ?></h3>
                    <p>Total Courses</p>
                </div>
            </div>

            <div class="stat-card stat-card--students">
                <span class="stat-card-icon">
                    <svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg>
                </span>
                <div>
                    <h3><?= $teacherStats['total_enrolled'] ?></h3>
                    <p>Total Enrolled</p>
                </div>
            </div>

            <div class="stat-card stat-card--rating">
                <span class="stat-card-icon">
                    <svg class="icon" aria-hidden="true"><use href="#i-star"></use></svg>
                </span>
                <div>
                    <h3><?= $teacherStats['average_rating'] ?></h3>
                    <p>Average Rating</p>
                </div>
            </div>

            <div class="stat-card stat-card--pending">
                <span class="stat-card-icon">
                    <svg class="icon" aria-hidden="true"><use href="#i-inbox"></use></svg>
                </span>
                <div>
                    <h3><?= $teacherStats['pending_courses'] ?></h3>
                    <p>Pending Courses</p>
                </div>
            </div>
        </div>

        <section class="dashboard-section">
            <div class="section-header">
                <h2>My Courses</h2>
                <a href="<?= BASEURL . '/teacher/courses' ?>" class="section-link">View all →</a>
            </div>

            <?php if (!empty($recentCourse)) : ?>

                <div class="course-grid course-grid--teacher">

                    <?php foreach ($recentCourse as $course) : ?>
                        <div class="course-card course-card--fill">
                            <div class="course-thumbnail">
                                <span class="course-category"><?= $course['category_name'] ?></span>
                                <span class="course-status-badge status-published"><?= $course['status'] ?></span>
                            </div>

                            <div class="course-content">
                                <p class="course-level"><?= ucfirst($course['difficulty']) ?></p>

                                <h3 class="course-title">
                                    <?= $course['course_name'] ?>
                                </h3>

                                <p class="course-description">
                                    <?= $course['short_description'] ?>
                                </p>

                                <div class="course-stats">
                                    <div class="stats-item">📚 <?= $course['total_materials'] ?></div>
                                    <div class="stats-item">👨‍🎓 <?= $course['total_students'] ?></div>
                                    <div class="stats-item">⭐ <?= $course['average_rating'] ?></div>
                                    <div class="stats-item">⏱ <?= timeAgo($course['created_at']) ?></div>
                                </div>

                                <div class="course-footer">
                                    <a href="<?= BASEURL . '/course/' . $course['course_id'] ?>" class="btn-course btn-course--full btn-outline">
                                        Manage
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>

            <?php else : ?>

                <div class="empty-state">

                    <div class="empty-state-content">

                        <div class="empty-state-icon">
                            📚
                        </div>

                        <h2 class="empty-state-title">
                            No Courses Yet
                        </h2>

                        <p class="empty-state-text">
                            You haven't created any courses yet. Start creating your first course and share your knowledge with students.
                        </p>

                        <a
                            href="<?= BASEURL . '/teacher/course/create' ?>"
                            class="btn btn-primary"
                        >
                            Create Course
                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </section>

        <section class="dashboard-section">
            <div class="section-header">
                <h2>Needs Your Attention</h2>
            </div>

            <div class="attention-grid">

                <div class="card attention-card">

                    <div class="attention-card-header">
                        <span class="attention-icon attention-icon--reject">
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-inbox"></use>
                            </svg>
                        </span>

                        <div>
                            <h3>Rejected Courses</h3>
                            <p>
                                <?= $countRejectedCourse ?>
                                <?= $countRejectedCourse === 1 ? 'course needs attention' : 'courses need attention' ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($rejectedCourses)) : ?>

                        <div class="attention-list">

                            <?php foreach ($rejectedCourses as $course) : ?>

                                <div class="attention-item">

                                    <div>
                                        <div class="attention-item-title">
                                            <?= $course['course_name'] ?>
                                        </div>

                                        <div class="attention-item-meta">
                                            Rejected <?= timeAgo($course['reviewed_at']) ?>
                                        </div>
                                    </div>

                                    <a
                                        href="<?= BASEURL . '/teacher/course/' . $course['course_id'] ?>"
                                        class="btn-course btn-outline"
                                    >
                                        Review
                                    </a>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else : ?>

                        <div class="empty-state empty-state--compact">

                            <div class="empty-state-content">

                                <div class="empty-state-icon">
                                    🎉
                                </div>

                                <h2 class="empty-state-title">
                                    No Rejected Courses
                                </h2>

                                <p class="empty-state-text">
                                    Great job! None of your courses require revisions at the moment.
                                </p>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

                <div class="card attention-card">

                    <div class="attention-card-header">

                        <span class="attention-icon attention-icon--grading">
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-check-square"></use>
                            </svg>
                        </span>

                        <div>

                            <h3>Assignments to Grade</h3>

                            <p>
                                <?= $totalSubmittedAssignments ?>
                                <?= $totalSubmittedAssignments === 1
                                    ? 'submission waiting'
                                    : 'submissions waiting' ?>
                            </p>

                        </div>

                    </div>

                    <?php if (!empty($pendingAssignments)) : ?>

                        <div class="attention-list">

                            <?php foreach ($submittedAssignments as $assignment) : ?>

                                <div class="attention-item">

                                    <div>

                                        <div class="attention-item-title">
                                            <?= $assignment['student_name'] ?>
                                        </div>

                                        <div class="attention-item-meta">
                                            <?= $assignment['assignment_title'] ?>
                                            ·
                                            <?= $assignment['course_name'] ?>
                                        </div>

                                    </div>

                                    <a
                                        href="<?= BASEURL . '/teacher/assignments/' . $assignment['assignment_attempt_id'] ?>"
                                        class="btn-course btn-primary"
                                    >
                                        Grade
                                    </a>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else : ?>

                        <div class="empty-state empty-state--compact">

                            <div class="empty-state-content">

                                <div class="empty-state-icon">
                                    ✅
                                </div>

                                <h2 class="empty-state-title">
                                    No Assignments to Grade
                                </h2>

                                <p class="empty-state-text">
                                    You're all caught up. There are no assignment submissions waiting for grading.
                                </p>

                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>
        </section>

        <section class="dashboard-section">

            <div class="section-header">
                <h2>Recent Activity</h2>
            </div>

            <div class="card activity-list">

                <?php if (!empty($recentActivities)) : ?>

                    <?php foreach ($recentActivities as $activity) : ?>

                        <div class="activity-item">

                            <span class="activity-icon
                                <?=
                                    $activity['activity_type'] === 'enroll'
                                        ? 'activity-icon--enroll'
                                        : ($activity['activity_type'] === 'review'
                                            ? 'activity-icon--review'
                                            : 'activity-icon--completed')
                                ?>
                            ">

                                <svg class="icon" aria-hidden="true">
                                    <use href="<?=
                                        $activity['activity_type'] === 'enroll'
                                            ? '#i-user-plus'
                                            : ($activity['activity_type'] === 'review'
                                                ? '#i-star'
                                                : '#i-check-circle')
                                    ?>"></use>
                                </svg>

                            </span>

                            <div class="activity-text">

                                <?php if ($activity['activity_type'] === 'enroll') : ?>

                                    <strong><?= $activity['user_name'] ?></strong>
                                    enrolled in
                                    <strong><?= $activity['course_name'] ?></strong>

                                <?php elseif ($activity['activity_type'] === 'completed') : ?>

                                    <strong><?= $activity['user_name'] ?></strong>
                                    completed
                                    <strong><?= $activity['course_name'] ?></strong>

                                <?php else : ?>

                                    <strong><?= $activity['user_name'] ?></strong>
                                    left a
                                    <strong><?= $activity['rating'] ?>★</strong>
                                    review on
                                    <strong><?= $activity['course_name'] ?></strong>

                                <?php endif; ?>

                            </div>

                            <span class="activity-time">
                                <?= timeAgo($activity['created_at']) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                <?php else : ?>

                    <div class="empty-state empty-state--compact">

                        <div class="empty-state-content">

                            <div class="empty-state-icon">
                                📈
                            </div>

                            <h2 class="empty-state-title">
                                No Recent Activity
                            </h2>

                            <p class="empty-state-text">
                                Student activity will appear here once learners start enrolling, completing courses, or leaving reviews.
                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </div>
</main>

<?php require 'app/views/layouts/footer.php' ?>