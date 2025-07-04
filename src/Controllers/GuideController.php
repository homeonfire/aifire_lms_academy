<?php
// src/Controllers/GuideController.php

class GuideController extends Controller {

    private $guideModel;
    private $favoriteModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }

        $this->guideModel = new Guide();
        $this->favoriteModel = new Favorite();
    }

    /**
     * Показывает страницу со списком всех гайдов.
     */
    public function index() {
        // 1. Получаем все гайды. Метод getAll() возвращает ВСЕ поля, включая cover_url.
        $guides = $this->guideModel->getAll();

        // 2. Получаем ID избранных гайдов для корректного отображения сердечек
        $userId = $_SESSION['user']['id'] ?? null;
        $favoritedGuideIds = [];
        if ($userId) {
            // Предполагаем, что у модели Favorite есть метод getFavoritedGuides
            $favoritedGuidesRaw = $this->favoriteModel->getFavoritedGuides($userId);
            $favoritedGuideIds = array_column($favoritedGuidesRaw, 'id');
        }

        // 3. Передаем в представление и гайды (с cover_url), и массив ID избранных
        $this->render('guides/index', [
            'title' => 'Все гайды',
            'guides' => $guides,
            'favoritedGuideIds' => $favoritedGuideIds
        ]);
    }

    /**
     * Показывает страницу конкретного гайда.
     * @param string $slug
     */
    public function show($slug) {
        // Метод findBySlug также возвращает ВСЕ поля, включая cover_url
        $guide = $this->guideModel->findBySlug($slug);

        if (!$guide) {
            http_response_code(404);
            die("Гайд не найден.");
        }

        $this->render('guides/show', [
            'title' => $guide['title'],
            'guide' => $guide
        ]);
    }
}