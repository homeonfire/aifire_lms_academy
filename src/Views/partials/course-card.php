<?php
// src/Views/partials/course-card.php

// 1. Проверяем cover_url, как и договаривались
$coverUrl = $course['cover_url'] ?? null;
$defaultImageUrl = '/public/uploads/zaglushka.png'; // <--- ВСТАВЬ СЮДА ССЫЛКУ
$imageUrlToShow = $coverUrl ?: $defaultImageUrl;

// --- Остальной код остается без изменений ---
$isFavorite = isset($favoritedCourseIds) && in_array($course['id'], $favoritedCourseIds);
$difficultyLevels = [
    'beginner' => 'Начинающий',
    'intermediate' => 'Средний',
    'advanced' => 'Продвинутый'
];
$difficultyText = $difficultyLevels[$course['difficulty_level']] ?? 'Не указан';
$categories = !empty($course['categories']) ? explode(',', $course['categories']) : [];
$companyLogoUrl = $course['company_logo'] ?? null;
$linkPath = ($course['type'] === 'masterclass') ? 'masterclass' : 'course';
?>
<div class="course-card-wrapper" style="position: relative;">
    <a href="/<?= $linkPath ?>/<?= $course['id'] ?>" class="course-card-link">
        <div class="course-card">
            <div class="course-card-image-wrapper course-item-cover" style="<?= getRandomGradient() ?>">
                <img src="<?= htmlspecialchars($imageUrlToShow) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                <div class="course-card-overlay-icons">
                    <?php if ($companyLogoUrl): ?>
                        <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="Company Logo" class="overlay-icon company-logo">
                    <?php endif; ?>
                </div>
            </div>

            <div class="course-card-content">
                <h4 class="course-card-title"><?= htmlspecialchars($course['title']) ?></h4>
                <span class="course-card-difficulty tag-difficulty-<?= $course['difficulty_level'] ?>"><?= $difficultyText ?></span>

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
            data-item-id="<?= $course['id'] ?>"
            data-item-type="course"
            title="Добавить в избранное">
        <svg viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"></path></svg>
    </button>
</div>