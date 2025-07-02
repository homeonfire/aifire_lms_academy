<?php
// src/Views/partials/course-card.php

$difficultyLevels = [
    'beginner' => 'Начинающий',
    'intermediate' => 'Средний',
    'advanced' => 'Продвинутый'
];
$difficultyText = $difficultyLevels[$course['difficulty_level']] ?? 'Не указан';
$categories = !empty($course['categories']) ? explode(',', $course['categories']) : [];
?>
<a href="/course/<?= $course['id'] ?>" class="course-card-link">
    <div class="course-card">
        <div class="course-card-image-wrapper">
            <img src="https://placehold.co/300x170/2A2A2A/FFFFFF?text=+" alt="<?= htmlspecialchars($course['title']) ?>">
            <?php /* You could add the bookmark icon here if desired, absolutely positioned */ ?>
            <div class="course-card-overlay-icons">
                <img src="/path/to/your/b-icon.png" alt="Company Logo" class="overlay-icon company-logo">
                <img src="/path/to/your/avatar-placeholder-1.png" alt="Instructor" class="overlay-icon instructor-avatar instructor-avatar-1">
                <img src="/path/to/your/avatar-placeholder-2.png" alt="Instructor" class="overlay-icon instructor-avatar instructor-avatar-2">
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