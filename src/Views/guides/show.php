<?php
$this->render('layouts/app-header', ['title' => $title]);
$embedUrl = getVideoEmbedUrl($guide['content_url']);
?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <div class="lesson-header">
                    <a href="/guides" class="back-link">← Назад ко всем гайдам</a>
                    <h1 class="lesson-title"><?= htmlspecialchars($guide['title']) ?></h1>
                </div>

                <div class="guide-content-area">
                    <?php if ($embedUrl): ?>
                        <div class="video-player-responsive" style="margin-bottom: 24px;">
                            <iframe src="<?= $embedUrl ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; encrypted-media" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($guide['content_text'])): ?>
                        <div class="lesson-description">
                            <?= nl2br(htmlspecialchars($guide['content_text'])) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>