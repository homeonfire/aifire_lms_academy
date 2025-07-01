<?php include_once "tpl/academy-header.php"; ?>
    <section class="header main-page">
        <div class="container">
            <div class="row">
                <div class="heading">
                    <h1 class="banner-heading"><?= htmlspecialchars($allLessons[0]->title_course) ?></h1>
                    <p class="banner-subheading"><?= htmlspecialchars($allLessons[0]->c_description) ?></p>
                </div>
            </div>
        </div>
    </section>
<?php if ($_SESSION['role']=='admin'): ?>
    <section class="admin-section">
        <div class="container">
            <a href="/courses/create?module_id=<?= htmlspecialchars($allLessons[0]->module_id) ?>" class="btn-add">
                Добавить модуль
            </a>
        </div>
    </section>
<?php endif; ?>
    <section>
        <div class="container">
            <div class="course-cards">
            <?php if (!empty($allLessons)): ?>
                <?php foreach ($allLessons as $item): ?>
                    <div class="course-card">
                        <div class="course-icon">📜</div>
                        <div class="course-info">
                            <div class="course-title"><?= htmlspecialchars($item->title) ?></div>
                            <div class="course-desc">...</div>
                            <a href="/courses/showLesson?lesson_id=<?= htmlspecialchars($item->id) ?>" class="btn-support">Перейти</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>У вас пока нет уроков</p>
            <?php endif; ?>
            </div>
        </div>
    </section>
<?php include_once "tpl/academy-footer.php"; ?>