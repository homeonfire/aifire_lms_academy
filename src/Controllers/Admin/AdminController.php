<?php
// src/Controllers/Admin/AdminController.php

class AdminController extends Controller {

    protected $userModel;

    public function __construct() {
        // Запускаем сессию, если она еще не запущена
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Проверяем, авторизован ли пользователь
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Проверяем, является ли пользователь администратором
        require_once __DIR__ . '/../../Models/User.php';
        $this->userModel = new User();
        $currentUser = $this->userModel->findById($_SESSION['user_id']);

        if (!$currentUser || $currentUser['role'] !== 'admin') {
            // Если не админ, выкидываем на главную
            http_response_code(403);
            die('Доступ запрещен');
        }
    }
}