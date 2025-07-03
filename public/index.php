<?php
// public/index.php

// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Запускаем сессию
session_start();
// 1. Обработка UTM-меток (с изменениями)
// Проверяем, есть ли UTM в GET-запросе И НЕ установлены ли они уже в сессии.
if (isset($_GET['utm_source']) && !isset($_SESSION['utm_data'])) {
    $_SESSION['utm_data'] = [
        'utm_source'   => $_GET['utm_source'] ?? null,
        'utm_medium'   => $_GET['utm_medium'] ?? null,
        'utm_campaign' => $_GET['utm_campaign'] ?? null,
        'utm_term'     => $_GET['utm_term'] ?? null,
        'utm_content'  => $_GET['utm_content'] ?? null,
    ];
}
// 2. Проверка уникальности визита
$cookieName = 'unique_visitor';
$isUnique = !isset($_COOKIE[$cookieName]);
// Если визит уникальный, устанавливаем cookie на 1 год
if ($isUnique) {
    setcookie($cookieName, '1', time() + (365 * 24 * 60 * 60), "/");
}

// 3. Сбор данных для логирования (с изменениями)
$visitData = [
    'ip_address'   => $_SERVER['REMOTE_ADDR'],
    'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    'is_unique'    => $isUnique ? 1 : 0,
    'user_id'      => $_SESSION['user']['id'] ?? null,
    'page_url'     => $_SERVER['REQUEST_URI'], // <-- ДОБАВЛЕНА СТРОКА ДЛЯ URL
    'utm_source'   => $_SESSION['utm_data']['utm_source'] ?? null,
    'utm_medium'   => $_SESSION['utm_data']['utm_medium'] ?? null,
    'utm_campaign' => $_SESSION['utm_data']['utm_campaign'] ?? null,
    'utm_term'     => $_SESSION['utm_data']['utm_term'] ?? null,
    'utm_content'  => $_SESSION['utm_data']['utm_content'] ?? null,
];



// --- ЦЕНТРАЛИЗОВАННОЕ ПОДКЛЮЧЕНИЕ ВСЕХ ФАЙЛОВ ---
// Конфигурация
require_once __DIR__ . '/../src/Config/database.php';

// Ядро
require_once __DIR__ . '/../src/Core/Controller.php';
require_once __DIR__ . '/../src/Core/Router.php';

// Модели
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Course.php';
require_once __DIR__ . '/../src/Models/Module.php';
require_once __DIR__ . '/../src/Models/Lesson.php';
require_once __DIR__ . '/../src/Models/Homework.php';
require_once __DIR__ . '/../src/Models/HomeworkAnswer.php';
require_once __DIR__ . '/../src/Models/LessonProgress.php';
require_once __DIR__ . '/../src/Models/Category.php';
require_once __DIR__ . '/../src/Models/Favorite.php';
require_once __DIR__ . '/../src/Models/Visit.php';

// Контроллеры (Пользовательская часть)
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/DashboardController.php';
require_once __DIR__ . '/../src/Controllers/CourseController.php';
require_once __DIR__ . '/../src/Controllers/LessonProgressController.php';
require_once __DIR__ . '/../src/Controllers/MyAnswersController.php';
require_once __DIR__ . '/../src/Controllers/HomeworkController.php';
require_once __DIR__ . '/../src/Controllers/HomeworkCheckController.php';
require_once __DIR__ . '/../src/Controllers/ProfileController.php';
require_once __DIR__ . '/../src/Controllers/FavoriteController.php';

// Контроллеры (Админ-панель)
require_once __DIR__ . '/../src/Controllers/Admin/AdminController.php';
require_once __DIR__ . '/../src/Controllers/Admin/DashboardController.php';
require_once __DIR__ . '/../src/Controllers/Admin/CourseController.php';
require_once __DIR__ . '/../src/Controllers/Admin/ModuleController.php';
require_once __DIR__ . '/../src/Controllers/Admin/LessonController.php';
require_once __DIR__ . '/../src/Controllers/Admin/UserController.php';
require_once __DIR__ . '/../src/Controllers/Admin/CategoryController.php';
require_once __DIR__ . '/../src/Controllers/Admin/VisitController.php';
// ----------------------------------------------------


// 4. Логирование в базу данных
try {
    $visitModel = new Visit();
    $visitModel->log($visitData);
} catch (Exception $e) {
    // В рабочей среде здесь можно логировать ошибку в файл, а не выводить на экран
    // error_log('Visit logging failed: ' . $e->getMessage());
}
// Запускаем роутер
$router = new Router();
$router->run();