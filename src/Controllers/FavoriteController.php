<?php
// src/Controllers/FavoriteController.php

class FavoriteController extends Controller {

    private $favoriteModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            // Для AJAX запросов лучше отправлять JSON с ошибкой
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401); // Unauthorized
                echo json_encode(['status' => 'error', 'message' => 'Требуется авторизация']);
            } else {
                header('Location: /login');
            }
            exit();
        }
        $this->favoriteModel = new Favorite();
    }

    /**
     * Переключает статус избранного для элемента (курс или урок)
     */
    public function toggle() {
        header('Content-Type: application/json');

        // Получаем данные из POST запроса
        $itemId = $_POST['item_id'] ?? null;
        $itemType = $_POST['item_type'] ?? null;
        $userId = $_SESSION['user']['id'];

        if (!$itemId || !$itemType || !in_array($itemType, ['course', 'lesson'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Неверные параметры']);
            exit();
        }

        $result = $this->favoriteModel->toggle($userId, $itemId, $itemType);

        echo json_encode(['status' => 'success', 'action' => $result]);
        exit();
    }

    /**
     * Отображает страницу со всеми избранными элементами
     */
    public function index() {
        $userId = $_SESSION['user']['id'];

        $favoritedCourses = $this->favoriteModel->getFavoritedCourses($userId);
        $favoritedLessons = $this->favoriteModel->getFavoritedLessons($userId);

        // Получаем ID курсов, чтобы передать их в partial
        $favoritedCourseIds = array_column($favoritedCourses, 'id');

        $data = [
            'title' => 'Мое избранное',
            'favoritedCourses' => $favoritedCourses,
            'favoritedLessons' => $favoritedLessons,
            'favoritedCourseIds' => $favoritedCourseIds // Добавляем ID в данные для вида
        ];

        $this->render('favorites/index', $data);
    }
}