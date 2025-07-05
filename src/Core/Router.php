<?php
// src/Core/Router.php

class Router {
    public function run() {
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        // CSRF проверка для POST запросов
        if ($method === 'POST' && !CSRF::checkPostRequest()) {
            http_response_code(403);
            die('CSRF токен недействителен или отсутствует');
        }

        switch (true) {
            // --- Главные роуты и авторизация ---
            case $uri === '/':
            case $uri === '/login':
                $controller = new AuthController();
                if ($method === 'GET') $controller->showLoginPage();
                elseif ($method === 'POST') $controller->loginUser();
                break;

            case $uri === '/register':
                $controller = new AuthController();
                if ($method === 'GET') $controller->showRegistrationPage();
                elseif ($method === 'POST') $controller->registerUser();
                break;

            case $uri === '/register/step2':
                $controller = new AuthController();
                if ($method === 'GET') $controller->showStep2();
                elseif ($method === 'POST') $controller->processStep2();
                break;

            case $uri === '/register/step3':
                $controller = new AuthController();
                if ($method === 'GET') $controller->showStep3();
                elseif ($method === 'POST') $controller->processStep3();
                break;

            case $uri === '/logout':
                (new AuthController())->logout();
                break;

            // --- Роуты пользовательской части ---
            case $uri === '/dashboard':
                (new DashboardController())->index();
                break;

            case $uri === '/courses':
                (new CourseController())->index();
                break;

            case preg_match('/^\/course\/(\d+)\/lesson\/(\d+)$/', $uri, $m):
                (new CourseController())->show($m[1], $m[2]);
                break;

            case preg_match('/^\/course\/(\d+)$/', $uri, $m):
                (new CourseController())->show($m[1]);
                break;

            case preg_match('/^\/lesson\/complete\/(\d+)$/', $uri, $m) && $method === 'POST':
                (new LessonProgressController())->complete($m[1]);
                break;

            case $uri === '/my-answers':
                (new MyAnswersController())->index();
                break;

            case $uri === '/profile' && $method === 'GET':
                (new ProfileController())->index();
                break;

            case $uri === '/profile/update' && $method === 'POST':
                (new ProfileController())->update();
                break;

            case $uri === '/homework/submit' && $method === 'POST':
                (new HomeworkController())->submit();
                break;

            case $uri === '/homework-check':
                (new HomeworkCheckController())->index();
                break;

            case preg_match('/^\/homework-check\/(\d+)$/', $uri, $m):
                $controller = new HomeworkCheckController();
                if ($method === 'GET') $controller->show($m[1]);
                elseif ($method === 'POST') $controller->processCheck($m[1]);
                break;

            // --- Роуты Админ-панели (с исправленными именами классов) ---
            case $uri === '/admin/dashboard':
                (new AdminDashboardController())->index();
                break;

            case $uri === '/admin/courses':
                (new AdminCourseController())->index();
                break;
            case $uri === '/admin/masterclasses': // <-- Добавлен новый путь
                (new AdminCourseController())->index();
                break;

            case $uri === '/admin/courses/new':
                (new AdminCourseController())->new();
                break;
            case $uri === '/admin/masterclasses/new': // <-- Добавлен новый путь
                (new AdminCourseController())->new();
                break;

            case $uri === '/admin/courses/create' && $method === 'POST':
                (new AdminCourseController())->create();
                break;

            // --- ДОБАВЬ ЭТОТ НОВЫЙ РОУТ ---
            case $uri === '/my-courses':
                (new MyCoursesController())->index();
                break;
            // --- КОНЕЦ НОВОГО РОУТА ---

            case preg_match('/^\/admin\/courses\/content\/(\d+)$/', $uri, $m):
                (new AdminCourseController())->content($m[1]);
                break;

            case preg_match('/^\/admin\/courses\/edit\/(\d+)$/', $uri, $m):
                (new AdminCourseController())->edit($m[1]);
                break;

            case preg_match('/^\/admin\/courses\/update\/(\d+)$/', $uri, $m) && $method === 'POST':
                (new AdminCourseController())->update($m[1]);
                break;

            case preg_match('/^\/admin\/courses\/delete\/(\d+)$/', $uri, $m):
                (new AdminCourseController())->delete($m[1]);
                break;

            case $uri === '/admin/modules/create' && $method === 'POST':
                (new AdminModuleController())->create();
                break;

            case $uri === '/admin/modules/update' && $method === 'POST':
                (new AdminModuleController())->update();
                break;

            case preg_match('/^\/admin\/modules\/delete\/(\d+)\/course\/(\d+)$/', $uri, $m):
                (new AdminModuleController())->delete($m[1], $m[2]);
                break;

            case $uri === '/admin/lessons/create' && $method === 'POST':
                (new AdminLessonController())->create();
                break;

            case $uri === '/admin/lessons/update' && $method === 'POST':
                (new AdminLessonController())->update();
                break;

            case preg_match('/^\/admin\/lessons\/delete\/(\d+)\/course\/(\d+)$/', $uri, $m):
                (new AdminLessonController())->delete($m[1], $m[2]);
                break;

            case preg_match('/^\/admin\/lessons\/edit-content\/(\d+)$/', $uri, $m):
                (new AdminLessonController())->editContent($m[1]);
                break;

            case preg_match('/^\/admin\/lessons\/save-content\/(\d+)$/', $uri, $m) && $method === 'POST':
                (new AdminLessonController())->saveContent($m[1]);
                break;

            case $uri === '/admin/users':
                (new AdminUserController())->index();
                break;

            case preg_match('/^\/admin\/users\/get\/(\d+)$/', $uri, $m):
                (new AdminUserController())->getUserJson($m[1]);
                break;

            case preg_match('/^\/admin\/users\/update\/(\d+)$/', $uri, $m) && $method === 'POST':
                (new AdminUserController())->updateUser($m[1]);
                break;

            case $uri === '/admin/categories' && $method === 'GET':
                (new AdminCategoryController())->index();
                break;

            case $uri === '/admin/categories/create' && $method === 'POST':
                (new AdminCategoryController())->create();
                break;

            case preg_match('/^\/admin\/categories\/update\/(\d+)$/', $uri, $m) && $method === 'POST':
                (new AdminCategoryController())->update($m[1]);
                break;

            case preg_match('/^\/admin\/categories\/delete\/(\d+)$/', $uri, $m) && $method === 'GET':
                (new AdminCategoryController())->delete($m[1]);
                break;

            case $uri === '/admin/visits' && $method === 'GET':
                (new AdminVisitController())->index();
                break;

            // Новый роут для карточки пользователя
            case preg_match('/^\/admin\/users\/show\/(\d+)$/', $uri, $m) && $method === 'GET':
                (new AdminUserController())->show($m[1]);
                break;

            // Новый роут для AJAX пагинации визитов
            case preg_match('/^\/admin\/users\/visits\/(\d+)$/', $uri, $m) && $method === 'GET':
                (new AdminUserController())->getVisitsJson($m[1]);
                break;

            // Обновляем роут для сохранения
            case preg_match('/^\/admin\/users\/update\/(\d+)$/', $uri, $m) && $method === 'POST':
                (new AdminUserController())->updateUser($m[1]);
                break;

            case $uri === '/favorites' && $method === 'GET':
                (new FavoriteController())->index();
                break;

            case $uri === '/favorite/toggle' && $method === 'POST':
                (new FavoriteController())->toggle();
                break;
            // --- КОНЕЦ НОВЫХ РОУТОВ ---

            case $uri === '/homework/submit' && $method === 'POST':
                (new HomeworkController())->submit();
                break;

            // --- Новые роуты для Мастер-классов ---
            case $uri === '/masterclasses':
                (new MasterclassController())->index();
                break;

            case preg_match('/^\/masterclass\/(\d+)\/lesson\/(\d+)$/', $uri, $m):
                (new MasterclassController())->show($m[1], $m[2]);
                break;

            case preg_match('/^\/masterclass\/(\d+)$/', $uri, $m):
                (new MasterclassController())->show($m[1]);
                break;

            case $uri === '/guides':
                (new GuideController())->index();
                break;

            case preg_match('/^\/guides\/([a-z0-9-]+)$/', $uri, $m):
                (new GuideController())->show($m[1]);
                break;

            case $uri === '/admin/guides':
                (new AdminGuideController())->index();
                break;

            case $uri === '/admin/guides/new' && $method === 'GET':
                (new AdminGuideController())->new();
                break;

            case $uri === '/admin/guides/create' && $method === 'POST':
                (new AdminGuideController())->create();
                break;

            case preg_match('/^\/admin\/guides\/edit\/(\d+)$/', $uri, $m) && $method === 'GET':
                (new AdminGuideController())->edit($m[1]);
                break;

            case preg_match('/^\/admin\/guides\/update\/(\d+)$/', $uri, $m) && $method === 'POST':
                (new AdminGuideController())->update($m[1]);
                break;

            case preg_match('/^\/admin\/guides\/delete\/(\d+)$/', $uri, $m) && $method === 'GET':
                (new AdminGuideController())->delete($m[1]);
                break;

            // --- Роут по умолчанию (404) ---
            default:
                http_response_code(404);
                echo "<h1>404 Страница не найдена</h1>";
                break;
        }
    }
}