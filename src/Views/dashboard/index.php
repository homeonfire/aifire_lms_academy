<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">

                <?php if ($featuredCourse): ?>
                    <div class="promo-block">
                        <div class="promo-thumbnail">
                            <img src="https://placehold.co/400x225/2A2A2A/FFFFFF?text=<?= urlencode(substr($featuredCourse['title'], 0, 10)) ?>" alt="<?= htmlspecialchars($featuredCourse['title']) ?>">
                            <span class="promo-badge">ПОЛУЧИТЬ СЕРТИФИКАТ</span>
                        </div>
                        <div class="promo-details">
                            <h2><?= htmlspecialchars($featuredCourse['title']) ?></h2>
                            <p><?= htmlspecialchars($featuredCourse['description']) ?></p>
                            <a href="/course/<?= $featuredCourse['id'] ?>" class="btn-primary">Начать курс</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($latestCourses)): ?>
                    <section class="content-section">
                        <h3 class="section-title">Новые курсы</h3>
                        <div class="content-carousel">
                            <?php if (!empty($latestCourses)): ?>
                                <?php foreach ($latestCourses as $course): ?>
                                    <?php $this->render('partials/course-card', ['course' => $course]); ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Курсы пока не добавлены.</p>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>