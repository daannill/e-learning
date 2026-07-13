<?php
    
$title = 'Teachers';

$styles = ['pages/user/teachers', 'components/navbar', 'components/button', 'components/header'];

$scripts = ['user_open'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?>

<section class="header">

    <div class="container">

        <div class="header-top">

            <div>

                <span class="header-badge">
                    Our Instructors
                </span>

                <h1 class="header-title">
                    Meet Our Teachers
                </h1>

                <p class="header-text">
                    Learn directly from experienced professionals and industry experts who have helped thousands of students improve their skills.
                </p>

            </div>

        </div>

        <div class="header-search">

            <input
                type="text"
                placeholder="Search wishlist..."
            >

        </div>

    </div>

</section>

<section class="course-filter-section">

    <div class="container">

        <div class="course-filter-wrapper">

            <div class="course-filter">

                <button class="filter-btn active">
                    All Teachers
                </button>

                <button class="filter-btn">
                    Most Students
                </button>

                <button class="filter-btn">
                    Most Rating
                </button>

            </div>
        </div>

    </div>

</section>

<div class="container">

    <div class="teacher-grid">

        <div class="teacher-card">

            <div class="teacher-cover"></div>

            <div class="teacher-content">

                <div class="teacher-avatar">
                    JD
                </div>

                <h3 class="teacher-name">
                    John Doe
                </h3>

                <p class="teacher-role">
                    Senior Backend Developer
                </p>

                <p class="teacher-bio">
                    Specialized in PHP, Laravel, MVC architecture, and scalable web application development.
                </p>

                <div class="teacher-meta">

                    <div>
                        <strong>12</strong>
                        <span>Courses</span>
                    </div>

                    <div>
                        <strong>4.2K</strong>
                        <span>Students</span>
                    </div>

                    <div>
                        <strong>4.9</strong>
                        <span>Rating</span>
                    </div>

                </div>

                <a href="#" class="btn btn-primary">
                    View Profile
                </a>

            </div>

        </div>

    </div>

</div>




<?php require 'app/views/layouts/footer.php' ?>