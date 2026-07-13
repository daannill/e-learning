<button
    type="button"
    class="mobile-toggle"
    onclick="document.querySelector('.dashboard-sidebar').classList.toggle('show')"
    aria-label="Open menu"
>
    <svg class="icon" aria-hidden="true"><use href="#i-menu"></use></svg>
</button>

<aside class="dashboard-sidebar">

    <div class="sidebar-brand">
        <span class="brand-mark">
            <svg class="icon" aria-hidden="true"><use href="#i-grid"></use></svg>
        </span>
        <span class="brand-name">E-Learning</span>
    </div>

    <div class="sidebar-back">
        <a href="<?= BASEURL . '/' ?>" class="back-link">
            <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
            Back to Home
        </a>
    </div>

    <div class="sidebar-profile">
        <img
            src="https://ui-avatars.com/api/?name=Budi+Santoso&background=2563eb&color=fff"
            alt="Budi Santoso"
            class="sidebar-avatar"
        >
        <div>
            <div class="sidebar-name"><?= authInfo('name') ?></div>
            <div class="sidebar-role"><?= ucfirst(authInfo('role')) ?></div>
        </div>
    </div>

    <nav class="sidebar-menu" aria-label="Teacher navigation">

        <div class="menu-group">
            <span class="menu-label">Overview</span>
            <a href="<?= BASEURL . '/teacher/dashboard' ?>" class="sidebar-item <?= active('teacher/dashboard') ?>">
                <svg class="icon" aria-hidden="true"><use href="#i-grid"></use></svg>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="menu-group">
            <span class="menu-label">Courses</span>
            <a href="<?= BASEURL . '/teacher/courses' ?>" class="sidebar-item <?= active('teacher/courses') ?>">
                <svg class="icon" aria-hidden="true"><use href="#i-book"></use></svg>
                <span>My Courses</span>
            </a>
            <a href="#" class="sidebar-item">
                <svg class="icon" aria-hidden="true"><use href="#i-archive"></use></svg>
                <span>Archived Courses</span>
            </a>
        </div>

        <div class="menu-group">
            <span class="menu-label">People</span>
            <a href="#" class="sidebar-item">
                <svg class="icon" aria-hidden="true"><use href="#i-users"></use></svg>
                <span>Students</span>
            </a>
            <a href="#" class="sidebar-item">
                <svg class="icon" aria-hidden="true"><use href="#i-star"></use></svg>
                <span>Reviews</span>
            </a>
        </div>

        <div class="menu-group">
            <span class="menu-label">Assessment</span>
            <a href="#" class="sidebar-item">
                <svg class="icon" aria-hidden="true"><use href="#i-clipboard"></use></svg>
                <span>Quiz Scores</span>
            </a>
            <a href="#" class="sidebar-item">
                <svg class="icon" aria-hidden="true"><use href="#i-check-square"></use></svg>
                <span>Grading</span>
            </a>
        </div>

    </nav>

    <div class="sidebar-footer">
        <a href="<?= BASEURL . '/logout' ?>" class="sidebar-item logout">
            <svg class="icon" aria-hidden="true"><use href="#i-log-out"></use></svg>
            <span>Logout</span>
        </a>
    </div>

</aside>