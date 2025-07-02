<?php
// src/Core/Router.php

class Router {
    public function run() {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        switch (true) { // Обратите внимание: true
            case $uri === '/':
                header('Location: /login');
                exit();

            case $uri === '/login' && $method === 'GET':
            case $uri === '/register' && $method === 'GET':
            case $uri === '/login' && $method === 'POST':
            case $uri === '/register' && $method === 'POST':
            case $uri === '/logout':
                require_once __DIR__ . '/../Controllers/AuthController.php';
                $authController = new AuthController();
                if ($uri === '/login' && $method === 'GET') $authController->showLoginPage();
                elseif ($uri === '/register' && $method === 'GET') $authController->showRegistrationPage();
                elseif ($uri === '/login' && $method === 'POST') $authController->loginUser();
                elseif ($uri === '/register' && $method === 'POST') $authController->registerUser();
                elseif ($uri === '/logout') $authController->logout();
                break;

            case $uri === '/dashboard':
                require_once __DIR__ . '/../Controllers/DashboardController.php';
                $dashboardController = new DashboardController();
                $dashboardController->index();
                break;

            // --- НОВЫЙ ДИНАМИЧЕСКИЙ РОУТ ---
            // --- НАЧАЛО НОВОГО БЛОКА РОУТОВ ДЛЯ КУРСОВ ---
            case preg_match('/^\/course\/(\d+)\/lesson\/(\d+)$/', $uri, $matches):
                require_once __DIR__ . '/../Controllers/CourseController.php';
                $courseController = new CourseController();
                $courseId = $matches[1];
                $lessonId = $matches[2];
                $courseController->show($courseId, $lessonId);
                break;

            case preg_match('/^\/course\/(\d+)$/', $uri, $matches):
                require_once __DIR__ . '/../Controllers/CourseController.php';
                $courseController = new CourseController();
                $courseId = $matches[1];
                $courseController->show($courseId); // Вызываем без lessonId
                break;
            // --- КОНЕЦ НОВОГО БЛОКА РОУТОВ ДЛЯ КУРСОВ ---
            // --- НАЧАЛО НОВОГО БЛОКА ДЛЯ ПРОФИЛЯ ---
            case $uri === '/profile' && $method === 'GET':
                require_once __DIR__ . '/../Controllers/ProfileController.php';
                $profileController = new ProfileController();
                $profileController->index();
                break;

            case $uri === '/profile/update' && $method === 'POST':
                require_once __DIR__ . '/../Controllers/ProfileController.php';
                $profileController = new ProfileController();
                $profileController->update();
                break;
            // --- КОНЕЦ НОВОГО БЛОКА ДЛЯ ПРОФИЛЯ ---
            // --- НОВЫЙ РОУТ ДЛЯ КАТАЛОГА КУРСОВ ---
            case $uri === '/courses':
                require_once __DIR__ . '/../Controllers/CourseController.php';
                $courseController = new CourseController();
                $courseController->index();
                break;
            // ------------------------------------
            // --- РОУТЫ АДМИН-ПАНЕЛИ ---
            case preg_match('/^\/admin\/dashboard/', $uri):
                require_once __DIR__ . '/../Controllers/Admin/DashboardController.php';
                $controller = new DashboardController();
                $controller->index();
                break;
            // -------------------------

            default:
                http_response_code(404);
                echo "<h1>404 Страница не найдена</h1>";
                break;
        }
    }
}