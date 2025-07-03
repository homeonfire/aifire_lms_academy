<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <h1 class="page-title">Гайды</h1>
                <p class="page-subtitle" style="margin-bottom: 30px;">Короткие и полезные инструкции на разные темы.</p>

                <div class="catalog-grid">
                    <?php if (!empty($guides)): ?>
                        <?php foreach ($guides as $guide): ?>
                            <?php // Передаем ID избранных в карточку ?>
                            <?php $this->render('partials/guide-card', [
                                'guide' => $guide,
                                'favoritedGuideIds' => $favoritedGuideIds ?? []
                            ]); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Гайды пока не добавлены.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>