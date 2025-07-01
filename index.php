<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_log', 'error.log'); // Указываем путь к лог-файлу
error_reporting(E_ALL);

require_once 'app/core/Router.php';
require_once 'app/config/db.php';
require_once 'app/config/telegram.php';
spl_autoload_register(function ($class_name) {
    $paths = [
        'app/core/',
        'app/controllers/',
        'app/models/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

session_start();

$router = new Router();

// Главная страница
$router->add('', ['controller' => 'LandController', 'action' => 'index']);

// Маршруты для авторизации и регистрации
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('register', ['controller' => 'AuthController', 'action' => 'register']);
$router->add('profile', ['controller' => 'AuthController', 'action' => 'profile']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);
$router->add('attach-email', ['controller' => 'AuthController', 'action' => 'attachEmail']);
$router->add('attach-telegram', ['controller' => 'AuthController', 'action' => 'attachTelegram']);

// Маршруты для курсов
$router->add('courses', ['controller' => 'CoursesController', 'action' => 'index']);
$router->add('courses/module', ['controller' => 'CoursesController', 'action' => 'modules']);
$router->add('courses/lesson', ['controller' => 'CoursesController', 'action' => 'lessons']);
$router->add('courses/create', ['controller' => 'CoursesController', 'action' => 'create']);
$router->add('courses/showLesson', ['controller' => 'CoursesController', 'action' => 'showLesson']);
$router->add('courses/createCourse', ['controller' => 'CoursesController', 'action' => 'createCourse']);
$router->add('courses/store', ['controller' => 'CoursesController', 'action' => 'storeLesson']);
$router->add('courses/storeCourse', ['controller' => 'CoursesController', 'action' => 'storeCourse']);
$router->add('courses/createModule', ['controller' => 'CoursesController', 'action' => 'createModule']);
$router->add('courses/storeModule', ['controller' => 'CoursesController', 'action' => 'storeModule']);

$url = isset($_GET['url']) ? $_GET['url'] : '';
$router->dispatch($url);