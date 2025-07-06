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

// Проверяем доступ к курсу (используем переданный hasAccess)
$hasAccess = $course['hasAccess'] ?? false;

// Определяем, бесплатный ли курс
$isFree = !empty($course['is_free']) || $course['price'] == 0;

// Определяем правильную ссылку для карточки
$cardLink = "/{$linkPath}/{$course['id']}";
if (!$isFree && !$hasAccess) {
    $cardLink = "/{$linkPath}/{$course['id']}/landing";
}
?>
<div class="course-card-wrapper" style="position: relative;">
    <a href="<?= $cardLink ?>" class="course-card-link">
        <div class="course-card">
            <div class="course-card-image-wrapper course-item-cover" style="<?= getRandomGradient() ?>">
                <img src="<?= htmlspecialchars($imageUrlToShow) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                <div class="course-card-overlay-icons">
                    <?php if ($companyLogoUrl): ?>
                        <img src="<?= htmlspecialchars($companyLogoUrl) ?>" alt="Company Logo" class="overlay-icon company-logo">
                    <?php endif; ?>
                </div>
                
                <!-- Цена курса -->
                <div class="course-price-badge">
                    <?php if ($isFree): ?>
                        <span class="price-free">Бесплатно</span>
                    <?php else: ?>
                        <span class="price-paid"><?= number_format($course['price'], 0, ',', ' ') ?> ₽</span>
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
                
                <!-- Кнопка действия -->
                <div class="course-card-action">
                    <?php if (isset($_SESSION['user'])): ?>
                        <?php if ($hasAccess): ?>
                            <span class="course-status-accessed">✓ Доступен</span>
                        <?php elseif ($isFree): ?>
                            <a href="/<?= $linkPath ?>/<?= $course['id'] ?>" class="btn btn-primary btn-small">Начать</a>
                        <?php else: ?>
                            <a href="/<?= $linkPath ?>/<?= $course['id'] ?>/landing" class="btn btn-primary btn-small">Купить</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($isFree): ?>
                            <a href="/auth/login" class="btn btn-primary btn-small">Начать</a>
                        <?php else: ?>
                            <a href="/auth/login" class="btn btn-primary btn-small">Купить</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
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

<style>
.course-price-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.price-free {
    color: #27ae60;
}

.price-paid {
    color: #f39c12;
}

.course-card-action {
    margin-top: 15px;
    text-align: center;
}

.course-status-accessed {
    display: inline-block;
    background: #27ae60;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.btn-small {
    padding: 8px 16px;
    font-size: 0.9rem;
}

.btn-primary {
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background: #0056b3;
    color: white;
}
</style>