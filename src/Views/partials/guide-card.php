<?php
// src/Views/partials/guide-card.php

// 1. Определяем, какую картинку показывать
$coverUrl = $guide['cover_url'] ?? null;
$defaultImageUrl = '/public/uploads/zaglushka.png'; // <--- ВСТАВЬ СЮДА ССЫЛКУ
$imageUrlToShow = $coverUrl ?: $defaultImageUrl;

$difficultyLevels = [
    'beginner' => 'Начинающий',
    'intermediate' => 'Средний',
    'advanced' => 'Продвинутый'
];
$difficultyText = $difficultyLevels[$guide['difficulty_level']] ?? 'Не указан';
$categories = !empty($guide['categories']) ? explode(',', $guide['categories']) : [];
$isFavorite = isset($favoritedGuideIds) && in_array($guide['id'], $favoritedGuideIds);
?>
<div class="course-card-wrapper" style="position: relative;">
    <a href="/guides/<?= htmlspecialchars($guide['slug']) ?>" class="course-card-link">
        <div class="course-card">
            <div class="course-card-image-wrapper guide-card-cover" style="<?= getRandomGradient() ?>">
                <img src="<?= htmlspecialchars($imageUrlToShow) ?>" alt="<?= htmlspecialchars($guide['title']) ?>">
            </div>
            <div class="course-card-content">
                <h4 class="course-card-title"><?= htmlspecialchars($guide['title']) ?></h4>
                <span class="course-card-difficulty tag-difficulty-<?= $guide['difficulty_level'] ?>"><?= $difficultyText ?></span>
                <?php if (!empty($categories)): ?>
                    <div class="course-card-categories">
                        <?= htmlspecialchars(implode(' | ', array_slice($categories, 0, 3))) ?><?php if (count($categories) > 3) echo '...'; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </a>

    <button
            class="favorite-toggle-btn <?= $isFavorite ? 'active' : '' ?>"
            data-item-id="<?= $guide['id'] ?>"
            data-item-type="guide"
            title="Добавить гайд в избранное">
        <svg viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"></path></svg>
    </button>
</div>