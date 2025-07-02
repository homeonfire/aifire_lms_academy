<?php
// src/Controllers/AuthController.php

class AuthController extends Controller {

    private $userModel;

    // Конструктор для загрузки модели
    public function __construct() {
        require_once __DIR__ . '/../Models/User.php';
        $this->userModel = new User();
    }

    public function showLoginPage() {
        $this->render('auth/login');
    }

    public function showRegistrationPage() {
        $this->render('auth/register');
    }

    public function registerUser() {
        // ... (ваш существующий код для registerUser) ...
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        $error = '';

        if (empty($email) || empty($password)) {
            $error = "Все поля обязательны для заполнения.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Некорректный формат email.";
        } elseif ($password !== $password_confirm) {
            $error = "Пароли не совпадают.";
        } elseif (strlen($password) < 6) {
            $error = "Пароль должен быть не менее 6 символов.";
        } elseif ($this->userModel->findByEmail($email)) {
            $error = "Пользователь с таким email уже существует.";
        }

        if ($error) {
            $this->render('auth/register', ['error' => $error]);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $success = $this->userModel->create($email, $passwordHash);

        if ($success) {
            header('Location: /login?status=registered');
            exit();
        } else {
            $this->render('auth/register', ['error' => 'Произошла ошибка при регистрации. Попробуйте снова.']);
        }
    }

    /**
     * Обрабатывает данные из формы входа
     */
    public function loginUser() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        // Проверяем, существует ли пользователь и верен ли пароль
        if ($user && password_verify($password, $user['password_hash'])) {
            // Успешный вход. Сохраняем ID пользователя в сессии.
            $_SESSION['user_id'] = $user['id'];

            // Перенаправляем на дашборд (пока что это будет пустая страница)
            header('Location: /dashboard');
            exit();
        } else {
            // Неуспешный вход. Показываем форму снова с ошибкой.
            $this->render('auth/login', ['error' => 'Неверный email или пароль.']);
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit();
    }
}