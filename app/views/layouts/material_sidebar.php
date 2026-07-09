<button class="mobile-toggle">
    ☰
</button>

<!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="course-header">

            <span class="course-category">
                <?= $course['category_name'] ?>
            </span>

            <h2 class="course-title">
                <?= $course['course_name'] ?>
            </h2>

        </div>

        <div class="course-progress">

            <div class="progress-top">

                <span>Progress</span>

                <span><?= $progress ?>%</span>

            </div>

            <div class="progress-bar">

                <div class="progress-fill" style="width: <?= $progress ?>%;"></div>

            </div>

        </div>

        <div class="material-list">

            <?php foreach ($materials as $item) : 

                $isCurrent = $item['material_id'] == $currentMaterial;

                $isLocked = $item['order_index'] > $maxMaterialAccess;

            ?>

                <a
                    href="<?= !$isLocked ?  BASEURL . '/material/' . $item['material_id'] : '#' ?>"
                    class="
                        material-item
                        <?= $item['is_completed'] ? 'completed' : '' ?>
                        <?= $isCurrent ? 'active' : '' ?>
                        <?= $isLocked ? 'locked' : '' ?>
                    "
                >

                    <div class="material-number">

                        <?php if($item['is_completed']) : ?>

                            ✓

                        <?php elseif($isLocked) : ?>

                            &#128274; 

                        <?php else : ?>

                            <?= str_pad(
                                $item['order_index'],
                                2,
                                '0',
                                STR_PAD_LEFT
                            ) ?>

                        <?php endif; ?>

                    </div>

                    <div class="material-content">

                        <div class="material-name">
                            <?= $item['title'] ?>
                        </div>

                        <div class="material-type">
                            <?= ucfirst($item['type']) ?>
                        </div>

                    </div>

                </a>

            <?php endforeach; ?>

            

            <!-- <a href="#" class="material-item active">

                <div class="material-number">
                    02
                </div>

                <div class="material-content">

                    <div class="material-name">
                        MVC Fundamentals
                    </div>

                    <div class="material-type">
                        Video
                    </div>

                </div>

            </a>

            <a href="#" class="material-item">

                <div class="material-number">
                    03
                </div>

                <div class="material-content">

                    <div class="material-name">
                        Routing System
                    </div>

                    <div class="material-type">
                        Video
                    </div>

                </div>

            </a>

            <a href="#" class="material-item">

                <div class="material-number">
                    04
                </div>

                <div class="material-content">

                    <div class="material-name">
                        Controllers
                    </div>

                    <div class="material-type">
                        Text
                    </div>

                </div>

            </a>

            <a href="#" class="material-item">

                <div class="material-number">
                    05
                </div>

                <div class="material-content">

                    <div class="material-name">
                        Authentication Quiz
                    </div>

                    <div class="material-type">
                        Quiz
                    </div>

                </div>

            </a> -->

        </div>

    </aside>