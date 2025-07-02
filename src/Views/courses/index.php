<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <div class="catalog-header">
                    <h1 class="page-title">Курсы</h1>
                </div>

                <div class="catalog-grid">
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <?php $this->render('partials/course-card', ['course' => $course]); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Курсы пока не добавлены.</p>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>