<?php
// src/Controllers/FavoriteController.php

class FavoriteController extends Controller {

    private $favoriteModel;

    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function __construct() {
        if (!isset($_SESSION['user'])) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => 'Требуется авторизация']);
            } else {
                header('Location: /login');
            }
            exit();
        }
        $this->favoriteModel = new Favorite();
    }

    public function toggle() {
        header('Content-Type: application/json');

        $itemId = $_POST['item_id'] ?? null;
        $itemType = $_POST['item_type'] ?? null;
        $userId = $_SESSION['user']['id'];

        if (!$itemId || !$itemType || !in_array($itemType, ['course', 'lesson', 'guide'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Неверные параметры']);
            exit();
        }

        $result = $this->favoriteModel->toggle($userId, $itemId, $itemType);

        echo json_encode(['status' => 'success', 'action' => $result]);
        exit();
    }
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---

    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    public function index() {
        $userId = $_SESSION['user']['id'];

        // Получаем все типы контента
        $favoritedCourses = $this->favoriteModel->getFavoritedCourses($userId, 'course');
        $favoritedMasterclasses = $this->favoriteModel->getFavoritedCourses($userId, 'masterclass'); // <-- Получаем мастер-классы
        $favoritedLessons = $this->favoriteModel->getFavoritedLessons($userId);
        $favoritedGuides = $this->favoriteModel->getFavoritedGuides($userId);

        // Собираем ID для корректной работы сердечек
        $favoritedCourseIds = array_column(array_merge($favoritedCourses, $favoritedMasterclasses), 'id');
        $favoritedGuideIds = array_column($favoritedGuides, 'id');

        $data = [
            'title' => 'Мое избранное',
            'favoritedCourses' => $favoritedCourses,
            'favoritedMasterclasses' => $favoritedMasterclasses, // <-- Передаем в шаблон
            'favoritedLessons' => $favoritedLessons,
            'favoritedGuides' => $favoritedGuides,
            'favoritedCourseIds' => $favoritedCourseIds,
            'favoritedGuideIds' => $favoritedGuideIds,
        ];

        $this->render('favorites/index', $data);
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---
}