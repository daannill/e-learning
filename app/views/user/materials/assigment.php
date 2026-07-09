<?php
    
$title = 'Quiz';

$styles = ['pages/user/quiz', 'components/navbar', 'components/button', 'components/material_sidebar'];

$scripts = ['user_open', 'material_sidebar_open', 'quiz_logic'];

?>

<?php require 'app/views/layouts/header.php' ?>

<?php require 'app/views/layouts/navbar.php' ?> 

<div class="learning-layout">

    <?php require 'app/views/layouts/material_sidebar.php' ?> 

    <!-- CONTENT -->
    <main class="content">

        <div class="assignment-container">

            <!-- HEADER -->

            <div class="assignment-card">

                <span class="assignment-badge">
                    📋 Assignment Material
                </span>

                <h1 class="assignment-title">
                    Build a Simple MVC Blog Application
                </h1>

                <p class="assignment-description">
                    Create a simple blog application using MVC architecture.
                    Implement CRUD functionality, database integration,
                    validation, and clean project structure.
                </p>

                <div class="assignment-meta">

                    <div class="meta-item">

                        <span class="meta-label">
                            Due Date
                        </span>

                        <span class="meta-value">
                            30 June 2026
                        </span>

                    </div>

                    <div class="meta-item">

                        <span class="meta-label">
                            Submission Type
                        </span>

                        <span class="meta-value">
                            ZIP File
                        </span>

                    </div>

                    <div class="meta-item">

                        <span class="meta-label">
                            Maximum Score
                        </span>

                        <span class="meta-value">
                            100 Points
                        </span>

                    </div>

                    <div class="meta-item">

                        <span class="meta-label">
                            Status
                        </span>

                        <span class="meta-value">
                            Not Submitted
                        </span>

                    </div>

                </div>

            </div>

            <!-- INSTRUCTION -->

            <div class="assignment-card">

                <h2 class="section-title">
                    Assignment Instructions
                </h2>

                <div class="assignment-content">

                    <p>
                        Complete the following project and submit your source code.
                    </p>

                    <h3>
                        Requirements
                    </h3>

                    <ul>

                        <li>Create a blog post list page.</li>

                        <li>Create add, edit and delete post features.</li>

                        <li>Use MVC architecture.</li>

                        <li>Store data in MySQL.</li>

                        <li>Apply validation.</li>

                        <li>Use Bootstrap for UI.</li>

                    </ul>

                    <h3>
                        Notes
                    </h3>

                    <p>
                        Submit your project as a ZIP file. Include a short explanation
                        about how your application works and screenshots of the final result.
                    </p>

                </div>

            </div>

            <!-- ANSWER -->

            <div class="assignment-card">

                <h2 class="section-title">
                    Assignment Explanation
                </h2>

                <div class="editor-wrapper">

                    <div class="editor-toolbar">
                        ✨ Rich Text Editor (Quill Area)
                    </div>

                    <div
                        class="editor-content"
                        contenteditable="true">
                    </div>

                </div>

                <h2 class="section-title">
                    Upload Assignment
                </h2>

                <div class="upload-area">

                    <div class="upload-icon">
                        📁
                    </div>

                    <div class="upload-title">
                        Upload Your Assignment File
                    </div>

                    <div class="upload-description">
                        ZIP, PDF, DOCX • Maximum 10 MB
                    </div>

                    <label class="upload-btn">

                        Choose File

                        <input
                            type="file"
                            class="file-input"
                            id="fileInput">

                    </label>

                    <div class="file-name" id="fileName">
                    </div>

                </div>

            </div>

            <!-- STATUS -->

            <div class="assignment-card">

                <div class="status-card">

                    <div>

                        <h3 style="margin-bottom:8px;">
                            Submission Status
                        </h3>

                        <p style="color:#64748b;">
                            Your assignment has not been submitted yet.
                        </p>

                    </div>

                    <span class="status-badge">
                        ⏳ Pending Submission
                    </span>

                </div>

            </div>

            <!-- FOOTER -->

            <div class="assignment-footer">

                <a href="#" class="btn btn-secondary">
                    ← Back to Material
                </a>

                <button class="btn btn-primary">
                    Submit Assignment
                </button>

            </div>

        </div>

    </main>
    
</div>
