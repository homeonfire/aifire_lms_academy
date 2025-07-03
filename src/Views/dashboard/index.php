<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title">Добро пожаловать, <?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'студент') ?>!</h1>
                    <p class="page-subtitle">Готовы к новым знаниям?</p>
                </div>

                <?php if ($featuredCourse): ?>
                    <div class="promo-block" style="position: relative;">
                        <?php
                        $isFavorite = isset($favoritedCourseIds) && in_array($featuredCourse['id'], $favoritedCourseIds);
                        ?>
                        <div class="promo-thumbnail">
                            <img src="https://placehold.co/400x225/2A2A2A/FFFFFF?text=<?= urlencode(substr($featuredCourse['title'], 0, 10)) ?>" alt="<?= htmlspecialchars($featuredCourse['title']) ?>">
                            <span class="promo-badge">ПОЛУЧИТЬ СЕРТИФИКАТ</span>
                        </div>
                        <div class="promo-details">
                            <h2><?= htmlspecialchars($featuredCourse['title']) ?></h2>
                            <p><?= htmlspecialchars($featuredCourse['description']) ?></p>
                            <a href="/course/<?= $featuredCourse['id'] ?>" class="btn-primary">Начать курс</a>
                        </div>
                        <button
                                class="favorite-toggle-btn <?= $isFavorite ? 'active' : '' ?>"
                                data-item-id="<?= $featuredCourse['id'] ?>"
                                data-item-type="course"
                                title="Добавить в избранное"
                                style="top: 24px; right: 24px;">
                            <svg viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"></path></svg>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($latestCourses)): ?>
                    <section class="content-section">
                        <h3 class="section-title">Новые курсы</h3>
                        <div class="catalog-grid">
                            <?php foreach ($latestCourses as $course): ?>
                                <?php $this->render('partials/course-card', [
                                    'course' => $course,
                                    'favoritedCourseIds' => $favoritedCourseIds ?? []
                                ]); ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($latestMasterclasses)): ?>
                    <section class="content-section">
                        <h3 class="section-title">Новые мастер-классы</h3>
                        <div class="catalog-grid">
                            <?php foreach ($latestMasterclasses as $course): ?>
                                <?php $this->render('partials/course-card', [
                                    'course' => $course,
                                    'favoritedCourseIds' => $favoritedCourseIds ?? []
                                ]); ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
                <?php if (!empty($latestGuides)): ?>
                    <section class="content-section">
                        <h3 class="section-title">Новые гайды</h3>
                        <div class="catalog-grid">
                            <?php foreach ($latestGuides as $guide): ?>
                                <?php // Передаем ID избранных гайдов в карточку ?>
                                <?php $this->render('partials/guide-card', [
                                    'guide' => $guide,
                                    'favoritedGuideIds' => $favoritedGuideIds ?? []
                                ]); ?>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>