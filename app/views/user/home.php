<?php
    
$title = 'Home';

$styles = ['pages/user/home', 'components/button', 'components/alert', 'components/navbar'];

$scripts = ['user_open'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?= successAlert() ?>

<?php require 'app/views/layouts/navbar.php' ?>


<!-- ======================================================
     HERO
====================================================== -->

<section class="hero">

    <div class="container hero-content">

        <div class="hero-text">

            <h1>
                Learn New Skills Online Easily
            </h1>

            <p>
                Explore high quality courses from experienced teachers and improve your skills anytime anywhere.
            </p>

            <div class="hero-buttons">

                <a href="<?= BASEURL ?>/courses" class="btn btn-primary">
                    Explore Courses
                </a>

                <a href="#" class="btn btn-outline">
                    Become Teacher
                </a>

            </div>

        </div>

        <div class="hero-image">
            E-Learning
        </div>

    </div>

</section>


<!-- ======================================================
     COURSES
====================================================== -->

<section class="section">

    <div class="container">

        <div class="section-header">

            <h2>
                Popular Courses
            </h2>

            <p>
                Start learning from our most popular courses
            </p>

        </div>

        <div class="course-grid">

            <div class="course-card">

                <!-- THUMBNAIL -->

                <div class="course-thumbnail">

                    <span class="course-category">
                        Development
                    </span>

                </div>


                <!-- CONTENT -->

                <div class="course-content">


                    <!-- LEVEL -->

                    <span class="course-level">
                        Intermediate
                    </span>


                    <!-- TITLE -->

                    <h2 class="course-title">
                        PHP MVC Masterclass
                    </h2>


                    <!-- DESCRIPTION -->

                    <p class="course-description">
                        Learn how to build scalable and clean PHP MVC applications from scratch.
                    </p>


                    <!-- STATS -->

                    <div class="course-stats">

                        <div class="stats-item">
                            📚 24 Materials
                        </div>

                        <div class="stats-item">
                            👨‍🎓 12.4K Students
                        </div>

                        <div class="stats-item">
                            ⏱ 8 Hours
                        </div>

                    </div>


                    <!-- FOOTER -->

                    <div class="course-footer">


                        <!-- TEACHER -->

                        <div class="teacher">

                            <img
                                src="https://i.pravatar.cc/100?img=12"
                                alt=""
                                class="teacher-avatar"
                            >

                            <div>

                                <div class="teacher-name">
                                    John Doe
                                </div>

                                <div class="teacher-role">
                                    Backend Developer
                                </div>

                            </div>

                        </div>


                        <!-- BUTTON -->

                        <a href="#" class="course-btn">
                            View Course
                        </a>

                    </div>

                </div>

            </div>

            <div class="course-card">

                <!-- THUMBNAIL -->

                <div class="course-thumbnail">

                    <span class="course-category">
                        Development
                    </span>

                </div>


                <!-- CONTENT -->

                <div class="course-content">


                    <!-- LEVEL -->

                    <span class="course-level">
                        Intermediate
                    </span>


                    <!-- TITLE -->

                    <h2 class="course-title">
                        PHP MVC Masterclass
                    </h2>


                    <!-- DESCRIPTION -->

                    <p class="course-description">
                        Learn how to build scalable and clean PHP MVC applications from scratch.
                    </p>


                    <!-- STATS -->

                    <div class="course-stats">

                        <div class="stats-item">
                            📚 24 Materials
                        </div>

                        <div class="stats-item">
                            👨‍🎓 12.4K Students
                        </div>

                        <div class="stats-item">
                            ⏱ 8 Hours
                        </div>

                    </div>


                    <!-- FOOTER -->

                    <div class="course-footer">


                        <!-- TEACHER -->

                        <div class="teacher">

                            <img
                                src="https://i.pravatar.cc/100?img=12"
                                alt=""
                                class="teacher-avatar"
                            >

                            <div>

                                <div class="teacher-name">
                                    John Doe
                                </div>

                                <div class="teacher-role">
                                    Backend Developer
                                </div>

                            </div>

                        </div>


                        <!-- BUTTON -->

                        <a href="#" class="course-btn">
                            View Course
                        </a>

                    </div>

                </div>

            </div>

            <div class="course-card">

                <!-- THUMBNAIL -->

                <div class="course-thumbnail">

                    <span class="course-category">
                        Development
                    </span>

                </div>


                <!-- CONTENT -->

                <div class="course-content">


                    <!-- LEVEL -->

                    <span class="course-level">
                        Intermediate
                    </span>


                    <!-- TITLE -->

                    <h2 class="course-title">
                        PHP MVC Masterclass
                    </h2>


                    <!-- DESCRIPTION -->

                    <p class="course-description">
                        Learn how to build scalable and clean PHP MVC applications from scratch.
                    </p>


                    <!-- STATS -->

                    <div class="course-stats">

                        <div class="stats-item">
                            📚 24 Materials
                        </div>

                        <div class="stats-item">
                            👨‍🎓 12.4K Students
                        </div>

                        <div class="stats-item">
                            ⏱ 8 Hours
                        </div>

                    </div>


                    <!-- FOOTER -->

                    <div class="course-footer">


                        <!-- TEACHER -->

                        <div class="teacher">

                            <img
                                src="https://i.pravatar.cc/100?img=12"
                                alt=""
                                class="teacher-avatar"
                            >

                            <div>

                                <div class="teacher-name">
                                    John Doe
                                </div>

                                <div class="teacher-role">
                                    Backend Developer
                                </div>

                            </div>

                        </div>


                        <!-- BUTTON -->

                        <a href="#" class="course-btn">
                            View Course
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ======================================================
     FEATURES
====================================================== -->

<section class="section">

    <div class="container">

        <div class="section-header">

            <h2>
                Why Choose Us
            </h2>

            <p>
                Everything you need to learn effectively online
            </p>

        </div>

        <div class="feature-grid">

            <div class="feature-card">

                <div class="feature-content">

                    <h3 class="feature-title">
                        Expert Teachers
                    </h3>

                    <p class="course-description">
                        Learn directly from experienced teachers and professionals in their field.
                    </p>

                </div>

            </div>

            <div class="feature-card">

                <div class="feature-content">

                    <h3 class="feature-title">
                        Flexible Learning
                    </h3>

                    <p class="course-description">
                        Study anytime and anywhere at your own pace without limitations.
                    </p>

                </div>

            </div>

            <div class="feature-card">

                <div class="feature-content">

                    <h3 class="feature-title">
                        Certificate
                    </h3>

                    <p class="course-description">
                        Get certificates after completing courses to showcase your skills.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ======================================================
     STATS
====================================================== -->

<section class="section">

    <div class="container">

        <div class="feature-grid">

            <div class="feature-card">

                <div class="feature-content" style="text-align:center;">

                    <h2 class="stats-number">
                        10K+
                    </h2>

                    <p>
                        Active Students
                    </p>

                </div>

            </div>

            <div class="feature-card">

                <div class="feature-content" style="text-align:center;">

                    <h2 class="stats-number">
                        120+
                    </h2>

                    <p>
                        Online Courses
                    </p>

                </div>

            </div>

            <div class="feature-card">

                <div class="feature-content" style="text-align:center;">

                    <h2 class="stats-number">
                        50+
                    </h2>

                    <p>
                        Professional Teachers
                    </p>

                </div>

            </div>

            <div class="feature-card">

                <div class="feature-content" style="text-align:center;">

                    <h2 class="stats-number">
                        95%
                    </h2>

                    <p>
                        Student Satisfaction
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ======================================================
     CTA
====================================================== -->

<section class="section">

    <div class="container">

        <div class="feature-card cta-card">

            <h2 class="cta-title">
                Start Learning Today
            </h2>

            <p class="cta-description">
                Join thousands of students and improve your skills with modern online learning.
            </p>

            <a href="#" class="btn cta-btn">
                Get Started
            </a>

        </div>

    </div>

</section>


<!-- ======================================================
     FOOTER
====================================================== -->

<footer class="footer">

    <div class="container">

        <div class="footer-content">

            <div>

                <h3 class="footer-title">
                    E-Learning
                </h3>

                <p>
                    Modern online learning platform for everyone.
                </p>

            </div>

            <div>

                <h3 class="footer-title">
                    Navigation
                </h3>

                <div class="footer-links">

                    <a href="#" class="footer-link">
                        Home
                    </a>

                    <a href="#" class="footer-link">
                        Courses
                    </a>

                    <a href="#" class="footer-link">
                        About
                    </a>

                </div>

            </div>

            <div>

                <h3 class="footer-title">
                    Support
                </h3>

                <div class="footer-links">

                    <a href="#" class="footer-link">
                        Help Center
                    </a>

                    <a href="#" class="footer-link">
                        Contact
                    </a>

                    <a href="#" class="footer-link">
                        Privacy Policy
                    </a>

                </div>

            </div>

        </div>

        <div class="footer-bottom">

            © 2026 E-Learning. All rights reserved.

        </div>

    </div>

</footer>

<?php require 'app/views/layouts/footer.php' ?>