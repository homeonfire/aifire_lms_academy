<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <h1 class="page-title">Все курсы</h1>
                <div class="catalog-grid">
                    <?php foreach ($courses as $course): ?>
                        <?php // Передаем ID избранных в каждую карточку
                        $this->render('partials/course-card', [
                            'course' => $course,
                            'favoritedCourseIds' => $favoritedCourseIds
                        ]); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>