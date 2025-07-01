<?php include_once "tpl/academy-header.php"; ?>

    <section class="header main-page">
        <div class="container">
            <div class="row">
                <div class="heading">
                    <h1 class="banner-heading"><?= htmlspecialchars($allModules[0]->title_course) ?></h1>
                    <p class="banner-subheading"><?= htmlspecialchars($allModules[0]->c_description) ?></p>
                </div>
            </div>
        </div>
    </section>
<?php if ($_SESSION['role']=='admin'): ?>
    <section class="admin-section">
        <div class="container">
            <a href="/courses/createModule?course_id=<?= htmlspecialchars($allModules[0]->course_id) ?>" class="btn-add">
                Добавить модуль
            </a>
        </div>
    </section>
<?php endif; ?>
    <section class="cards-container">
        <div class="container">
                <div class="course-cards">
                    <?php if (!empty($allModules)): ?>
                        <?php foreach ($allModules as $item): ?>
                            <div class="course-card">
                                <div class="course-icon">📜</div>
                                <div class="course-info">
                                    <div class="course-title"><?= htmlspecialchars($item->title) ?></div>
                                    <div class="course-desc"><?= nl2br(htmlspecialchars($item->description)) ?></div>
                                    <a href="/courses/lesson?lId=<?= htmlspecialchars($item->id) ?>" class="btn-support">Перейти</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>У вас пока нет курсов</p>
                    <?php endif; ?>
                </div>
        </div>
    </section>
<?php include_once "tpl/academy-footer.php"; ?>