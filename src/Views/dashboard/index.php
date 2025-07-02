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
                            <?php foreach ($latestCourses as $course): ?>
                                <div class="content-card">
                                    <img src="https://placehold.co/300x170/2A2A2A/FFFFFF?text=<?= urlencode(substr($course['title'], 0, 10)) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                                    <h4><?= htmlspecialchars($course['title']) ?></h4>
                                    <span>Курс</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>