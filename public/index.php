<?php
// public/index.php

// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Запускаем сессию
session_start();

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
// ----------------------------------------------------



// Запускаем роутер
$router = new Router();
$router->run();