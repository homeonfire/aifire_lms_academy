<?php
// src/Controllers/Admin/UserController.php

class AdminUserController extends AdminController {

    public function __construct() {
        parent::__construct();
        // Модель User уже подключена в родительском AdminController
    }

    /**
     * Показывает список всех пользователей
     */
    public function index() {
        $users = $this->userModel->getAll();
        $this->renderAdminPage('admin/users/index', [
            'title' => 'Управление пользователями',
            'users' => $users
        ]);
    }

    /**
     * Обрабатывает смену роли пользователя
     */
    public function changeRole() {
        $userId = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? null;

        // Дополнительная проверка, чтобы админ не мог случайно изменить свою роль
        if ($userId && $userId != $_SESSION['user']['id']) {
            $this->userModel->updateRole($userId, $role);
        }

        // Возвращаем на страницу со списком пользователей
        header('Location: /admin/users');
        exit();
    }

    // Добавьте этот метод в класс UserController (в админке)
    /**
     * Возвращает данные пользователя в формате JSON для модального окна
     */
    public function getUserJson($id) {
        $user = $this->userModel->findById($id);
        if ($user) {
            // Отправляем заголовок, что это JSON
            header('Content-Type: application/json');
            // Убираем хэш пароля для безопасности
            unset($user['password_hash']);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Пользователь не найден']);
        }
        exit(); // Важно завершить выполнение скрипта
    }

    // Добавьте этот метод в класс UserController (в админке)
    public function updateUser($id) {
        // Проверка, чтобы админ не мог изменить роль сам себе
        if ($id == $_SESSION['user']['id'] && $_POST['role'] !== 'admin') {
            header('Location: /admin/users'); // Просто игнорируем попытку
            exit();
        }

        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'role' => $_POST['role'] ?? 'user',
            'experience_level' => $_POST['experience_level'] ?? 'beginner',
            'preferred_skill_type' => $_POST['preferred_skill_type'] ?? ''
        ];

        $this->userModel->updateUserData($id, $data);

        header('Location: /admin/users');
        exit();
    }
}