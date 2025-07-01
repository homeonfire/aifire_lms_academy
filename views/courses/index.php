<?php include_once "tpl/academy-header.php"; ?>

        <section class="header main-page">
            <div class="container">
                <div class="row">
                    <div class="heading">
                        <h1 class="banner-heading">Добро пожаловать в AI Fire LMS</h1>
                        <p class="banner-subheading">Тут вы можете увидеть все курсы, которые Вам доступны</p>
                    </div>
                </div>

            </div>
        </section>

<?php if ($_SESSION['role']=='admin'): ?>
    <section class="admin-section">
        <div class="container">
            <a href="/courses/createCourse" class="btn-add">
                Добавить курс
            </a>
        </div>
    </section>
<?php endif; ?>
        <section class="cards-container">
            <div class="container">
                <div class="course-cards">
                    <?php if (!empty($allCourses)): ?>
                        <?php foreach ($allCourses as $item): ?>
                            <div class="course-card">
                                <div class="course-icon"><?= htmlspecialchars($item->emojis) ?></div>
                                <div class="course-info">
                                    <div class="course-title"><?= htmlspecialchars($item->title) ?></div>
                                    <div class="course-desc"><?= nl2br(htmlspecialchars($item->description)) ?></div>
                                    <a href="/courses/module?mId=<?= htmlspecialchars($item->id) ?>" class="btn-support">Перейти</a>
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