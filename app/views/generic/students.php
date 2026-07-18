<?php

$title = 'My Courses';

$styles = [
    'components/button',
    'components/course_card',
    'components/teacher_layout',
    'components/teacher_sidebar',
    'components/header',
    'components/pagination',
    'pages/teacher/students_card'
];

$scripts = ['filter'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/partials/alert.php' ?>
<?php require 'app/views/partials/icon.php' ?>
<?php require 'app/views/layouts/teacher_sidebar.php' ?>

<main class="dashboard-content">
    <div class="dashboard-container">

        <div class="dashboard-topbar">
            <div class="dashboard-greeting">
                <h1>Students</h1>
                <p>Monitor student enrollments, learning progress, and course participation.</p>
            </div>
        </div>

        <section class="dashboard-section">

            <div class="course-filter-section">

                <form
                    method="GET"
                    id="filter-form"
                >

                    <div class="course-search">

                        <svg class="icon" aria-hidden="true">
                            <use href="#i-search"></use>
                        </svg>

                        <input
                            type="search"
                            name="search"
                            placeholder="Search your courses..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>
                    
                    <div class="filter-section filter-toolbar">

                        <div class="filter-group">

                            <input
                                type="hidden"
                                id="status"
                                name="status"
                                value="<?= $status ?>"
                            >

                            <button
                                type="button"
                                class="filter-btn <?= $status === 'all' ? 'active' : '' ?>"
                                onclick="setStatus('all')"
                            >
                                All
                            </button>

                            <button
                                type="button"
                                class="filter-btn <?= $status === 'in progress' ? 'active' : '' ?>"
                                onclick="setStatus('in progress')"
                            >
                                In Progress
                            </button>

                            <button
                                type="button"
                                class="filter-btn <?= $status === 'completed' ? 'active' : '' ?>"
                                onclick="setStatus('completed')"
                            >
                                Completed
                            </button>
                        </div>

                        <div class="filter-select">

                            <select
                                name="sort"
                                onchange="this.form.requestSubmit()"
                            >

                                <option
                                    value="newest"
                                    <?= $sort === 'newest' ? 'selected' : '' ?>
                                >
                                    Newest
                                </option>

                                <option
                                    value="oldest"
                                    <?= $sort === 'oldest' ? 'selected' : '' ?>
                                >
                                    Oldest
                                </option>

                            </select>

                        </div>

                    </div>

                </form>

            </div>

            <div class="progress-list-scroll">
                <div class="progress-list">
                    <?php if (!empty($students)) : ?>

                        <?php foreach ($students as $s) : ?>

                            <div class="progress-item">

                                <div class="progress-student">
                                    <div class="progress-avatar">DA</div>

                                    <div>
                                        <div class="progress-student-name">
                                            <?= $s['student_name'] ?>
                                        </div>

                                        <div class="progress-course-line">
                                            <?= $s['course_name'] ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="progress-field">
                                    <span class="progress-field-label">
                                        Progress
                                    </span>

                                    <span class="progress-field-value">
                                        <?= $s['progress'] ?>%
                                    </span>

                                    <div class="progress-bar-track">
                                        <div
                                            class="progress-bar-fill is-progress"
                                            style="width:<?= $s['progress'] ?>%"
                                        ></div>
                                    </div>
                                </div>

                                <div class="progress-field">
                                    <span class="progress-field-label">
                                        Status
                                    </span>

                                    <span class="progress-status status-progress">
                                        <?= $s['progress_status'] ?>
                                    </span>
                                </div>

                                <div class="progress-field">
                                    <span class="progress-field-label">
                                        Last Open
                                    </span>

                                    <span class="progress-field-value">
                                        <?= timeAgo($s['last_open']) ?>
                                    </span>
                                </div>

                                <div class="progress-field">
                                    <span class="progress-field-label">
                                        Enrolled
                                    </span>

                                    <span class="progress-field-value">
                                        <?= timeAgo($s['enrolled_at']) ?>
                                    </span>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div class="empty-state">

                            <div class="empty-state-content">

                                <div class="empty-state-icon">
                                    👨‍🎓
                                </div>

                                <h2 class="empty-state-title">
                                    No Students Yet
                                </h2>

                                <p class="empty-state-text">
                                    No students match your current filters, or none have enrolled in your published courses yet.
                                </p>

                            </div>

                        </div>

                    <?php endif; ?>
                    
                </div>
            </div>

            
            <?php if (count($students) > 0 ) : ?>
                <?php include APP_PATH . '/app/views/partials/pagination.php' ?>
            <?php endif; ?>
            
        </section>

    </div>
</main>

<?php require 'app/views/layouts/footer.php' ?>