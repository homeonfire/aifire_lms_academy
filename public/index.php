<?php
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Запускаем сессию
session_start();

// Подключаем необходимые файлы
require_once __DIR__ . '/../src/Core/Controller.php';
require_once __DIR__ . '/../src/Core/Router.php';

// Запускаем роутер
$router = new Router();
$router->run();