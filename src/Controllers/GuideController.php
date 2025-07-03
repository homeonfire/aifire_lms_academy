<?php
// src/Controllers/GuideController.php

// УДАЛИТЕ ИЛИ ЗАКОММЕНТИРУЙТЕ ЭТИ СТРОКИ, если они были добавлены:
// use App\Models\Guide;
// use App\Models\Favorite;

class GuideController extends Controller {

    private $guideModel;
    private $favoriteModel; // ДОБАВЛЕНО: Объявляем favoriteModel

    public function __construct() {
        // ИЗМЕНЕНО: Проверка сессии пользователя.
        // Если вы хотите, чтобы доступ к гайдам был только у авторизованных, оставьте эту проверку.
        // Если гайды могут быть доступны и без авторизации, то уберите ее.
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
        // ИЗМЕНЕНО: Используем \Guide() и \Favorite() для глобального пространства имен
        $this->guideModel = new \Guide();
        $this->favoriteModel = new \Favorite(); // ДОБАВЛЕНО: Создаем экземпляр Favorite Model
    }

    /**
     * Показывает страницу со списком всех гайдов.
     */
    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    public function index() {
        $guides = $this->guideModel->getAll();

        // Получаем ID избранных гайдов для корректного отображения сердечек
        $userId = $_SESSION['user']['id'] ?? null; // Безопасное получение user ID
        $favoritedGuideIds = [];
        if ($userId) {
            $favoritedGuidesRaw = $this->favoriteModel->getFavoritedGuides($userId);
            $favoritedGuideIds = array_column($favoritedGuidesRaw, 'id');
        }


        $this->render('guides/index', [
            'title' => 'Все гайды',
            'guides' => $guides,
            'favoritedGuideIds' => $favoritedGuideIds
        ]);
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---

    /**
     * Показывает страницу конкретного гайда.
     * @param string $slug
     */
    public function show($slug) {
        $guide = $this->guideModel->findBySlug($slug);

        if (!$guide) {
            http_response_code(404);
            die("Гайд не найден.");
        }

        // Логика content_json уже обрабатывается, так как findBySlug возвращает все поля.
        // Здесь не требуется дополнительных изменений.

        $this->render('guides/show', [
            'title' => $guide['title'],
            'guide' => $guide
        ]);
    }
}