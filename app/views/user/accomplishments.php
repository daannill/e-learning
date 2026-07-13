<?php

$title = 'Explore Course';

$styles = ['components/navbar', 'components/button', 'components/header', 'pages/user/accomplishments'];

$scripts = ['user_open'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?>

<section class="header">

    <div class="container">

        <div class="header-top">

            <div>

                <span class="header-badge">
                    Student Achievements
                </span>

                <h1 class="header-title">
                    Accomplishments
                </h1>

                <p class="header-text">
                    Track your learning progress, certificates, and achievements earned throughout your journey.
                </p>

            </div>

        </div>

    </div>

</section>

<div class="container">

    <h2 class="section-title">
        Recent Certificates
    </h2>

    <div class="certificate-list">

        <div class="certificate-card">

            <div class="certificate-info">

                <h3>
                    PHP MVC Masterclass
                </h3>

                <p>
                    Completed on May 12, 2026
                </p>

            </div>

            <a href="#" class="btn btn-primary">
                View Certificate
            </a>

        </div>

        <div class="certificate-card">

            <div class="certificate-info">

                <h3>
                    UI UX Design Fundamentals
                </h3>

                <p>
                    Completed on April 28, 2026
                </p>

            </div>

            <a href="#" class="btn btn-primary">
                View Certificate
            </a>

        </div>

    </div>
    
</div>

<?php require 'app/views/layouts/footer.php' ?>