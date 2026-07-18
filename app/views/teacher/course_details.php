<?php

$title = $course['course_name'];
 
$styles = [
    'components/button',
    'components/course_card',
    'components/teacher_layout',
    'components/teacher_sidebar',
    'components/modal'
];
 
$scripts = ['open_modal'];

?>

<?php require 'app/views/layouts/header.php' ?>
 
<?php require 'app/views/partials/alert.php' ?>
<?php require 'app/views/partials/icon.php' ?>
<?php require 'app/views/layouts/teacher_sidebar.php' ?>
 
<main class="dashboard-content">
    <div class="dashboard-container">
 
        <div class="page-back-link">
            <a href="<?= BASEURL . '/teacher/courses' ?>" class="back-link">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"></use></svg>
                Kembali ke My Courses
            </a>
        </div>
 
        <!-- ================= HERO: COURSE INFO ================= -->
        <div class="dashboard-section">
            <div class="course-card--hero">
 
                <div class="course-thumbnail course-thumbnail--lg">
                    <?php if (!empty($course['thumbnail'])): ?>
                        <img 
                            src="<?= BASEURL . '/uploads/course-thumbnails/' . $course['thumbnail'] ?>" 
                            alt="<?= $course['course_name'] ?>" 
                            class="course-thumbnail__img"
                        >
                    <?php endif; ?>

                    <span class="course-category">
                        <?= $course['category_name'] ?>
                    </span>
                    <span class="course-status-badge status-<?= $course['status'] ?>">
                        <?= ucfirst($course['status']) ?>
                    </span>
                </div>
 
                <div class="course-content course-content--lg">
 
                    <p class="course-level">
                        <?= ucfirst($course['difficulty']) ?>
                    </p>
 
                    <h1><?= $course['course_name'] ?></h1>
 
                    <p class="mt-1" style="max-width:640px;">
                        <?= $course['description'] ?>
                    </p>
 
                    <div class="course-stats mt-2">
                        <div class="stats-item">📚 <?= $course['total_materials'] ?> Materi</div>
                        <div class="stats-item">👨‍🎓 <?= $course['total_students'] ?> Siswa</div>
                        <div class="stats-item">⭐ <?= $course['average_rating'] ?> Rating</div>
                        <div class="stats-item">⏱  <?= timeAgo($course['created_at']) ?></div>
                    </div>
 
                    <div class="flex items-center gap-1">
 
                        <?php if ($course['status'] === 'draft') : ?>

                            <a
                                href="<?= BASEURL . '/edit/course/' . $course['course_id'] ?>"
                                class="btn btn-outline btn-sm"
                            >
                                <svg class="icon" aria-hidden="true">
                                    <use href="#i-edit"></use>
                                </svg>
                                Edit Course
                            </a>

                            <form
                                method="POST"
                                action="<?= BASEURL . '/teacher/course/archive/' . $course['course_id'] ?>"
                            >
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-archive"></use>
                                    </svg>
                                    Archive Course
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="<?= BASEURL . '/teacher/course/publish/' . $course['course_id'] ?>"
                                style="margin-left: auto;"
                            >
                                <button type="submit" class="btn btn-green btn-sm">
                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-check-circle"></use>
                                    </svg>
                                    Publish
                                </button>
                            </form>

                        <?php elseif ($course['status'] === 'published') : ?>

                            <form
                                method="POST"
                                action="<?= BASEURL . '/teacher/course/draft/' . $course['course_id'] ?>"
                            >
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-draft"></use>
                                    </svg>
                                    Draft
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="<?= BASEURL . '/teacher/course/archive/' . $course['course_id'] ?>"
                            >
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <svg class="icon" aria-hidden="true">
                                        <use href="#i-archive"></use>
                                    </svg>
                                    Archive
                                </button>
                            </form>
                        <?php endif; ?>
 
                    </div>
 
                </div>
            </div>
        </div>
 
        <!-- ================= MATERIALS ================= -->
        <section class="dashboard-section">
 
            <div class="section-header">
                <h2>Course Materials</h2>

                <button type="button" class="btn btn-primary btn-sm" onclick="openModal('materialTypeModal')">
                    <svg class="icon" aria-hidden="true"><use href="#i-plus"></use></svg>
                    Add Material
                </button>
            </div>
 
            <?php if (!empty($materials)) : ?>
 
                <div class="card">
                    <div class="attention-list attention-list--divided">
 
                        <?php foreach ($materials as $index => $material) : ?>
 
                            <div class="attention-item">
 
                                <div class="flex items-center gap-1">
 
                                    <div class="attention-icon attention-icon--<?= $material['type'] ?>">
                                        <svg class="icon" aria-hidden="true">
                                            <use href="#<?= materialIcon($material['type']) ?>"></use>
                                        </svg>
                                    </div>
 
                                    <div>
                                        <p class="attention-item-title">
                                            <?= $index + 1 ?>. <?= $material['title'] ?>
                                        </p>
                                        <p class="attention-item-meta">
                                            <?= ucfirst($material['type']) ?>
                                        </p>
                                    </div>
 
                                </div>
 
                                <div class="flex items-center gap-1">
 
                                    <a
                                        href="<?= BASEURL . '/view/' . $material['type'] . '/' . $material['material_id'] ?>"
                                        class="btn-course btn-outline"
                                    >
                                        View
                                    </a>

                                    <a
                                        href="<?= BASEURL . '/edit/' . $material['type'] . '/' . $material['material_id'] ?>"
                                        class="btn-course btn-primary"
                                    >
                                        Edit
                                    </a>
 
                                    <form
                                        method="POST"
                                        action="<?= BASEURL . '/delete/material/' . $material['material_id'] ?>"
                                        onsubmit="return confirm('Delete material ini?')"
                                    >
                                        <?= csrf() ?>
                                        <button type="submit" class="btn-course btn-secondary">
                                            Delete
                                        </button>
                                    </form>
 
                                </div>
 
                            </div>
 
                        <?php endforeach; ?>
 
                    </div>
                </div>
 
            <?php else : ?>
 
                <div class="card">
                    <div class="empty-state">

                        <div class="empty-state-content">

                            <div class="empty-state-icon">
                                📄
                            </div>

                            <h3 class="empty-state-title">
                                No Materials Yet
                            </h3>

                            <p class="empty-state-text">
                                This course doesn't have any materials yet. Add your first material to start building the course.
                            </p>

                        </div>

                    </div>
                </div>
 
            <?php endif; ?>
 
        </section>
 
    </div>
</main>

<div class="modal-overlay" id="materialTypeModal">
    <div class="modal">
        <div class="modal-header">
            <h3>Add Material</h3>
            <button type="button" class="modal-close" onclick="closeModal('materialTypeModal')" aria-label="Close">
                <svg class="icon" aria-hidden="true"><use href="#i-x"></use></svg>
            </button>
        </div>
        <p class="modal-subtitle">Choose the type of material you want to add.</p>
 
        <div class="material-type-grid">
            <a href="<?= BASEURL . '/create/video/' . $course['course_id'] ?>" class="material-type-option material-type-option--video">
                <span class="material-type-icon"><svg class="icon" aria-hidden="true"><use href="#i-video"></use></svg></span>
                <span>Video</span>
            </a>
            <a href="<?= BASEURL . '/create/text/' . $course['course_id'] ?>" class="material-type-option material-type-option--text">
                <span class="material-type-icon"><svg class="icon" aria-hidden="true"><use href="#i-file-text"></use></svg></span>
                <span>Text</span>
            </a>
            <a href="<?= BASEURL . '/create/quiz/' . $course['course_id'] ?>" class="material-type-option material-type-option--quiz">
                <span class="material-type-icon"><svg class="icon" aria-hidden="true"><use href="#i-clipboard"></use></svg></span>
                <span>Quiz</span>
            </a>
            <a href="<?= BASEURL . '/create/assignment/' . $course['course_id'] ?>" class="material-type-option material-type-option--assignment">
                <span class="material-type-icon"><svg class="icon" aria-hidden="true"><use href="#i-check-square"></use></svg></span>
                <span>Assignment</span>
            </a>
        </div>
    </div>
</div>
 
<?php require 'app/views/layouts/footer.php' ?>