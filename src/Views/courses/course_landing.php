<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['title']) ?> - Лендинг</title>
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/landing.css"> </head>
<body>

<div class="landing-container">
    <header class="landing-header">
        <h1><?= htmlspecialchars($course['title']) ?></h1>
        <p class="author">Автор курса: <?= htmlspecialchars($course['author_name']) // Предполагается, что у вас есть имя автора ?></p>
    </header>

    <main class="landing-main">
        <div class="landing-hero">
            <img src="<?= htmlspecialchars($course['preview_image_url']) // Путь к главному изображению ?>" alt="<?= htmlspecialchars($course['title']) ?>">
        </div>

        <section class="landing-description">
            <h2>О курсе</h2>
            <div>
                <?= $course['description'] // Описание из Editor.js, выводится как есть ?>
            </div>
        </section>

        <section class="landing-features">
            <h2>Что вы получите на курсе?</h2>
            <ul>
                <li>✅ Доступ к материалам навсегда</li>
                <li>✅ Поддержка от кураторов</li>
                <li>✅ Сертификат о прохождении</li>
            </ul>
        </section>
    </main>

    <footer class="landing-footer">
        <h2>Готовы начать?</h2>
        <p class="price">Стоимость: <?= htmlspecialchars($course['price']) ?> ₽</p>
        <a href="/course/<?= $course['id'] ?>/buy" class="btn btn-primary btn-lg">Купить курс</a>
    </footer>
</div>

</body>
</html>