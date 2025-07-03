<?php
// src/Controllers/GuideController.php

class GuideController extends Controller {

    private $guideModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
        $this->guideModel = new Guide();
    }

    /**
     * Показывает страницу со списком всех гайдов.
     */
    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    public function index() {
        $guides = $this->guideModel->getAll();

        // Получаем ID избранных гайдов для корректного отображения сердечек
        $favoriteModel = new Favorite();
        $userId = $_SESSION['user']['id'];
        $favoritedGuidesRaw = $favoriteModel->getFavoritedGuides($userId);
        $favoritedGuideIds = array_column($favoritedGuidesRaw, 'id');

        $this->render('guides/index', [
            'title' => 'Все гайды',
            'guides' => $guides,
            'favoritedGuideIds' => $favoritedGuideIds // <-- Передаем ID в шаблон
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

        $this->render('guides/show', [
            'title' => $guide['title'],
            'guide' => $guide
        ]);
    }
}