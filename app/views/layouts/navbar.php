<!-- ======================================================
     NAVBAR
====================================================== -->

<nav class="navbar">

    <div class="container nav-content">

        <a href="<?= BASEURL ?>/" class="logo">
            E-Learning
        </a>

        <div class="nav-menu">

            <a href="<?= BASEURL ?>/" class="nav-link <?= active('/') ?>">
                Home
            </a>

            <a href="<?= BASEURL ?>/courses" class="nav-link <?= active('courses') ?>">
                Courses
            </a>

            <a href="<?= BASEURL ?>/teachers" class="nav-link <?= active('teachers') ?>">
                Teachers
            </a>

            <a href="<?= BASEURL ?>/mylearning" class="nav-link <?= active('mylearning') ?>">
                My Learning
            </a>

            <a href="#" class="nav-link">
                About
            </a>

        </div>

        <?php if(guest()): ?>

            <div class="flex gap-1">

                <a href="<?= BASEURL ?>/login" class="btn btn-outline">
                    Login
                </a>

                <a href="<?= BASEURL ?>/register" class="btn btn-primary">
                    Register
                </a>

            </div>

        <?php endif; ?>

        <?php if(auth()): ?>
            
            <div class="user-wrapper">
    
                <!-- BUTTON -->
    
                <div class="user-profile" id="userButton">
    
                    <img
                        src="https://i.pravatar.cc/100"
                        alt=""
                        class="user-avatar"
                    >
    
                    <div class="user-info">
    
                        <span class="user-name">
                            <?= authInfo('name') ?>
                        </span>
    
                        <span class="user-role">
                            <?= ucfirst(authInfo('role')) ?>
                        </span>
    
                    </div>
    
                </div>
    
    
                <!-- DROPDOWN -->
    
                <div class="user-dropdown" id="dropdownMenu">
    
    
                    <!-- HEADER -->
    
                    <div class="dropdown-header">
    
                        <img
                            src="https://i.pravatar.cc/100"
                            alt=""
                            class="dropdown-avatar"
                        >
    
                        <h3 class="dropdown-name">
                            <?= authInfo('name') ?>
                        </h3>
    
                        <p class="dropdown-email">
                            <?= authInfo('email') ?>
                        </p>
    
                    </div>
    
    
                    <!-- MENU -->
    
                    <div class="dropdown-menu">
    
                        <a href="<?= BASEURL ?>/myprofile" class="dropdown-item">
                            👤 My Profile
                        </a>
    
                        <a href="<?= BASEURL ?>/mylearning" class="dropdown-item">
                            📚 My Learning
                        </a>
    
                        <a href="<?= BASEURL ?>/wishlist" class="dropdown-item">
                            ❤️ Wishlist
                        </a>
    
                        <a href="<?= BASEURL ?>/accomplishments" class="dropdown-item">
                            🏆 Accomplishments
                        </a>
    
                        <a href="<?= BASEURL ?>/logout" class="dropdown-item logout">
                            🚪 Logout
                        </a>
    
                    </div>
    
                </div>
    
            </div>
        
        <?php endif; ?>

    </div>

</nav>