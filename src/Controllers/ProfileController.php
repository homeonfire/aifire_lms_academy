<?php
// src/Controllers/ProfileController.php

class ProfileController extends Controller {

    private $userModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

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
        // Получаем текущие данные пользователя, чтобы сохранить старый путь к аватару, если новый не будет загружен
        $currentUser = $this->userModel->findById($_SESSION['user']['id']);

        $data = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'experience_level' => $_POST['experience_level'] ?? 'beginner',
            'preferred_skill_type' => $_POST['preferred_skill_type'] ?? '',
            'avatar_path' => $currentUser['avatar_path'] // Сохраняем старый путь по умолчанию
        ];

        // --- ЛОГИКА ЗАГРУЗКИ АВАТАРА ---
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '-' . basename($_FILES['avatar']['name']);
            $targetPath = $uploadDir . $fileName;

            // Проверка типа файла
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['avatar']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                    // Обновляем путь в данных для сохранения в БД
                    $data['avatar_path'] = '/public/uploads/avatars/' . $fileName;

                    // Удаляем старый аватар, если он был и это не дефолтный
                    if (!empty($currentUser['avatar_path']) && strpos($currentUser['avatar_path'], 'default-avatar.png') === false) {
                        $oldAvatarFullPath = __DIR__ . '/../../' . ltrim($currentUser['avatar_path'], '/');
                        if (file_exists($oldAvatarFullPath)) {
                            unlink($oldAvatarFullPath);
                        }
                    }
                }
            }
        }
        // --- КОНЕЦ ЛОГИКИ ЗАГРУЗКИ ---

        $success = $this->userModel->update($_SESSION['user']['id'], $data);

        // Обновляем данные в сессии, чтобы аватар сразу обновился в интерфейсе
        if ($success) {
            $updatedUser = $this->userModel->findById($_SESSION['user']['id']);
            $_SESSION['user'] = $updatedUser;
        }

        header('Location: /profile');
        exit();
    }
}