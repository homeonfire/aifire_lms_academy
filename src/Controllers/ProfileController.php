<?php
// src/Controllers/ProfileController.php

class ProfileController extends Controller {

    private $userModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) { // ИСПРАВЛЕНО
            header('Location: /login');
            exit();
        }
        require_once __DIR__ . '/../Models/User.php';
        $this->userModel = new User();
    }

    /**
     * Показывает страницу профиля
     */
    public function index() {
        $user = $this->userModel->findById($_SESSION['user']['id']);

        $data = [
            'title' => 'Мой профиль',
            'user' => $user
        ];

        $this->render('profile/index', $data);
    }

    /**
     * Обрабатывает обновление профиля
     */
    public function update() {
        // Получаем данные из формы
        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'experience_level' => $_POST['experience_level'] ?? 'beginner',
            'preferred_skill_type' => $_POST['preferred_skill_type'] ?? ''
        ];

        // Вызываем метод модели для обновления
        $success = $this->userModel->update($_SESSION['user']['id'], $data); // ИСПРАВЛЕНО

        // Перенаправляем обратно на страницу профиля
        // В будущем можно добавить сообщение об успехе
        header('Location: /profile');
        exit();
    }
}