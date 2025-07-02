<?php
// src/Views/partials/course-card.php
?>
<a href="/course/<?= $course['id'] ?>" class="course-card-link">
    <div class="content-card">
        <img src="https://placehold.co/300x170/2A2A2A/FFFFFF?text=+" alt="<?= htmlspecialchars($course['title']) ?>">

        <div class="card-content">
            <div class="card-main-info">
                <h4><?= htmlspecialchars($course['title']) ?></h4>
                <span>Курс</span>
            </div>
            <div class="certificate-badge">ПОЛУЧИТЬ СЕРТИФИКАТ</div>
        </div>
    </div>
</a>