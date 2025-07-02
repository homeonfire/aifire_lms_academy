<?php
// src/Views/partials/course-card.php

$difficultyLevels = [
    'beginner' => 'Начинающий',
    'intermediate' => 'Средний',
    'advanced' => 'Продвинутый'
];
$difficultyText = $difficultyLevels[$course['difficulty_level']] ?? 'Не указан';
$categories = !empty($course['categories']) ? explode(',', $course['categories']) : [];

// Динамические URL для изображений. Замените на ваши реальные данные.
// Если $course['image_url'] не существует, будет использоваться placehold.co
$imageUrl = $course['image_url'] ?? 'https://placehold.co/300x170/2A2A2A/FFFFFF?text=+';
$companyLogoUrl = $course['company_logo'] ?? 'https://placehold.co/48x48/000/FFFFFF?text=B'; // Пример
$instructorAvatar1 = $course['instructor_avatar_1'] ?? 'https://placehold.co/40x40/555/FFFFFF?text=I1'; // Пример
$instructorAvatar2 = $course['instructor_avatar_2'] ?? 'https://placehold.co/40x40/555/FFFFFF?text=I2'; // Пример
?>
<a href="/course/<?= $course['id'] ?>" class="course-card-link">
    <div class="course-card">
        <div class="course-card-image-wrapper">
            <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
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