<?php $this->render('layouts/app-header', ['title' => $title]); ?>

<div class="app-layout">
    <?php $this->render('layouts/app-sidebar'); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title">Добро пожаловать, <?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'студент') ?>!</h1>
                <p class="page-subtitle">Готовы к новым знаниям?</p>
            </div>

            <?php if (!empty($startedCourses)): ?>
                <section class="content-section">
                    <div class="section-header">
                        <h3 class="section-title">Продолжить обучение</h3>
                        <div class="slider-controls">
                            <button class="slider-btn prev-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-5 w-5"><path d="m15 18-6-6 6-6"></path></svg></button>
                            <button class="slider-btn next-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-5 w-5"><path d="m9 18 6-6-6-6"></path></svg></button>
                        </div>
                    </div>
                    <div class="catalog-grid">
                        <?php foreach ($startedCourses as $course): ?>
                            <?php $this->render('partials/course-card', [
                                'course' => $course,
                                'favoritedCourseIds' => $favoritedCourseIds ?? []
                            ]); ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php elseif ($featuredCourse): ?>
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
                    <div class="section-header">
                        <h3 class="section-title">Новые курсы</h3>
                        <div class="slider-controls">
                            <button class="slider-btn prev-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-5 w-5"><path d="m15 18-6-6 6-6"></path></svg></button>
                            <button class="slider-btn next-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-5 w-5"><path d="m9 18 6-6-6-6"></path></svg></button>
                        </div>
                    </div>
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
                    <div class="section-header">
                        <h3 class="section-title">Новые мастер-классы</h3>
                        <div class="slider-controls">
                            <button class="slider-btn prev-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-5 w-5"><path d="m15 18-6-6 6-6"></path></svg></button>
                            <button class="slider-btn next-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-5 w-5"><path d="m9 18 6-6-6-6"></path></svg></button>
                        </div>
                    </div>
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
                    <div class="section-header">
                        <h3 class="section-title">Новые гайды</h3>
                        <div class="slider-controls">
                            <button class="slider-btn prev-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-5 w-5"><path d="m15 18-6-6 6-6"></path></svg></button>
                            <button class="slider-btn next-btn"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-5 w-5"><path d="m9 18 6-6-6-6"></path></svg></button>
                        </div>
                    </div>
                    <div class="catalog-grid">
                        <?php foreach ($latestGuides as $guide): ?>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sections = document.querySelectorAll('.content-section');

        sections.forEach(section => {
            const slider = section.querySelector('.catalog-grid');
            const prevBtn = section.querySelector('.prev-btn');
            const nextBtn = section.querySelector('.next-btn');

            if (!slider || !prevBtn || !nextBtn) return;

            const scrollAmount = 320; // Ширина одной карточки (300px) + отступ (20px)

            nextBtn.addEventListener('click', () => {
                slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            prevBtn.addEventListener('click', () => {
                slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        });
    });
</script>