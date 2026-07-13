<?php

$title = 'My Courses';

$styles = [
    'components/button',
    'components/course_card',
    'components/teacher_layout',
    'components/teacher_sidebar',
    'components/header',
    'components/pagination'
];

$scripts = ['filter'];

var_dump($totalCourses);

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/partials/alert.php' ?>
<?php require 'app/views/partials/icon.php' ?>
<?php require 'app/views/layouts/teacher_sidebar.php' ?>

<main class="dashboard-content">
    <div class="dashboard-container">

        <div class="dashboard-topbar">
            <div class="dashboard-greeting">
                <h1>My Courses</h1>
                <p>Manage and track all the courses you've created.</p>
            </div>
            <a href="#" class="btn btn-primary">
                <svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg>
                Add Course
            </a>
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
                                class="filter-btn <?= $status === 'published' ? 'active' : '' ?>"
                                onclick="setStatus('published')"
                            >
                                Published
                            </button>

                            <button
                                type="button"
                                class="filter-btn <?= $status === 'draft' ? 'active' : '' ?>"
                                onclick="setStatus('draft')"
                            >
                                Draft
                            </button>

                            <button
                                type="button"
                                class="filter-btn <?= $status === 'pending' ? 'active' : '' ?>"
                                onclick="setStatus('pending')"
                            >
                                Pending Review
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

                                <option
                                    value="most_students"
                                    <?= $sort === 'most_students' ? 'selected' : '' ?>
                                >
                                    Most Students
                                </option>

                                <option
                                    value="highest_rating"
                                    <?= $sort === 'highest_rating' ? 'selected' : '' ?>
                                >
                                    Highest Rated
                                </option>

                            </select>

                        </div>

                    </div>

                </form>

            </div>

            <?php if (!empty($courses)) : ?>

                <div class="course-grid">

                    <?php foreach ($courses as $course) : ?>

                        <div class="course-card course-card--fill">

                            <div class="course-thumbnail">

                                <span class="course-category">
                                    <?= $course['category_name'] ?>
                                </span>

                                <span class="course-status-badge status-<?= $course['status'] ?>">
                                    <?= ucfirst($course['status']) ?>
                                </span>

                            </div>

                            <div class="course-content">

                                <p class="course-level">
                                    <?= ucfirst($course['difficulty']) ?>
                                </p>

                                <h3
                                    class="course-title"
                                    title="<?= htmlspecialchars($course['course_name']) ?>"
                                >
                                    <?= $course['course_name'] ?>
                                </h3>

                                <p
                                    class="course-description"
                                    title="<?= htmlspecialchars($course['short_description']) ?>"
                                >
                                    <?= $course['short_description'] ?>
                                </p>

                                <div class="course-stats">

                                    <div class="stats-item">
                                        📚 <?= $course['total_materials'] ?>
                                    </div>

                                    <div class="stats-item">
                                        👨‍🎓 <?= $course['total_students'] ?>
                                    </div>

                                    <div class="stats-item">
                                        ⭐ <?= $course['average_rating'] ?>
                                    </div>

                                    <div class="stats-item">
                                        ⏱ <?= timeAgo($course['created_at']) ?>
                                    </div>

                                </div>

                                <div class="course-footer">

                                    <a
                                        href="<?= BASEURL . '/teacher/course/' . $course['course_id'] ?>"
                                        class="btn-course btn-course--full btn-outline"
                                    >
                                        Manage
                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php elseif ($totalCourses === 0 && $status == '' && $sort == '') : ?>

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

            <?php else : ?>

                <div class="empty-state">

                    <div class="empty-state-content">

                        <div class="empty-state-icon">
                            📚
                        </div>

                        <h2 class="empty-state-title">
                            No Courses Found
                        </h2>

                        <p class="empty-state-text">
                            No courses match your current filters. Try changing the search or filter options.
                        </p>

                    </div>

                </div>

            <?php endif; ?>

            <?php if ($totalCourses > 0) : ?>

                <div class="pagination">

                    <?php if ($page > 1) : ?>

                        <a
                            href="<?= buildQuery(['page' => $page - 1]) ?>"
                            class="pagination-btn pagination-nav"
                            aria-label="Previous page"
                        >
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-left"></use>
                            </svg>
                        </a>

                    <?php else : ?>

                        <button
                            type="button"
                            class="pagination-btn pagination-nav"
                            disabled
                        >
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-left"></use>
                            </svg>
                        </button>

                    <?php endif; ?>


                    <?php foreach (paginate($page, $totalPages) as $item) : ?>

                        <?php if ($item === '...') : ?>

                            <span class="pagination-ellipsis">
                                &hellip;
                            </span>

                        <?php else : ?>

                            <a
                                href="<?= buildQuery(['page' => $item]) ?>"
                                class="pagination-btn <?= $item === $page ? 'active' : '' ?>"
                                <?= $item === $page ? 'aria-current="page"' : '' ?>
                            >
                                <?= $item ?>
                            </a>

                        <?php endif; ?>

                    <?php endforeach; ?>


                    <?php if ($page < $totalPages) : ?>

                        <a
                            href="<?= buildQuery(['page' => $page + 1]) ?>"
                            class="pagination-btn pagination-nav"
                            aria-label="Next page"
                        >
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </a>

                    <?php else : ?>

                        <button
                            type="button"
                            class="pagination-btn pagination-nav"
                            disabled
                        >
                            <svg class="icon" aria-hidden="true">
                                <use href="#i-arrow-right"></use>
                            </svg>
                        </button>

                    <?php endif; ?>

                </div>

            <?php endif; ?>
            
        </section>

    </div>
</main>

<?php require 'app/views/layouts/footer.php' ?>