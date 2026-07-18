<?php

$title = 'Wishlist';

$styles = ['components/navbar', 'components/course_card', 'components/header', 'components/button'];

$scripts = ['user_open', 'save_course_button', 'filter'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?>

<!-- ======================================================
     HEADER
====================================================== -->

<section class="header">

    <div class="container">

        <div class="header-top">

            <div>

                <span class="header-badge">
                    Saved Courses
                </span>

                <h1 class="header-title">
                    Your Wishlist
                </h1>

                <p class="header-text">
                    Save courses you are interested in and continue learning anytime you want.
                </p>

            </div>

        </div>

    </div>

</section>

<section class="course-filter-section">

    <div class="container">

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
                            name="category"
                            value="<?= $category ?>"
                        >

                        <button
                            type="button"
                            class="filter-btn <?= $category === 'all' ? 'active' : '' ?>"
                            onclick="setStatus('all')"
                        >
                            All
                        </button>

                        <?php foreach ($categories as $item) : ?>

                            <button
                                type="button"
                                class="filter-btn <?= $category == $item['category_id'] ? 'active' : '' ?>"
                                onclick="setStatus('<?= $item['category_id'] ?>')"
                            >
                                <?= htmlspecialchars($item['category_name']) ?>
                            </button>

                        <?php endforeach; ?>

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

    </div>

</section>



<!-- ======================================================
     GRID
====================================================== -->

<section>

    <div class="container">

        <?php if (!empty($courses)) : ?>

            <div class="course-grid">
                
                <?php foreach ($courses as $course) : ?>

                    <div class="course-card">

                        <div class="course-thumbnail">

                            <span class="course-category">
                                <?= $course['category_name'] ?>
                            </span>
                            
                            <?php if(auth()): ?>

                                <button data-csrf-token="<?= csrfToken() ?>" data-course-id="<?= $course['course_id'] ?>" class="course-save <?= $course['is_saved'] ? 'active' : '' ?>">
                                    ♥
                                </button>

                            <?php endif; ?>

                        </div>

                        <div class="course-content">

                            <p class="course-level"><?= ucfirst($course['difficulty']) ?></p>

                            <h3 class="course-title" title="<?= $course['course_name'] ?>">
                                <?= $course['course_name'] ?>
                            </h3>

                            <p class="course-description" title="<?= $course['short_description'] ?>">
                                <?= $course['short_description'] ?>
                            </p>

                            <div class="course-stats">

                                <div class="stats-item">
                                    📚 <?= $course['total_materials'] ?>
                                </div>

                                <div class="stats-item">
                                    👨‍🎓 <?= numberShort($course['total_students']) ?>
                                </div>
                                
                                <div class="stats-item">
                                    ⭐ <?= $course['average_rating'] ?>
                                </div>

                                <div class="stats-item">
                                    ⏱ <?= timeAgo($course['created_at']) ?>
                                </div>

                            </div>

                            <div class="course-footer">

                                <div class="teacher">

                                    <img
                                        src="https://i.pravatar.cc/100?img=20"
                                        alt=""
                                        class="teacher-avatar"
                                    >

                                    <div>

                                        <div class="teacher-name">
                                            <?= $course['teacher_name'] ?>
                                        </div>

                                        <div class="teacher-role">
                                            <?= $course['job_title'] ?>
                                        </div>

                                    </div>

                                </div>

                                <?php if ($course['is_enrolled']) : ?>

                                    <a href="<?= BASEURL ?>/course/<?= $course['course_id'] ?>" class="btn-course btn-green">
                                        Continue
                                    </a>

                                <?php else : ?>
                                    
                                    <a href="<?= BASEURL ?>/course/<?= $course['course_id'] ?>" class="btn-course btn-primary">
                                        View
                                    </a>
                                    
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>
                    
                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <?php if (empty($courses)) : ?>

            <div class="empty-state">

                <div class="empty-state-content">

                    <div class="empty-state-icon">
                        🤍
                    </div>

                    <h2 class="empty-state-title">
                        Wishlist is Empty
                    </h2>

                    <p class="empty-state-text">
                        You haven't saved any courses yet. Start exploring and save your favorite courses.
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

</section>

<?php require 'app/views/layouts/footer.php' ?>