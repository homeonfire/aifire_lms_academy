<?php
// src/Controllers/Admin/GuideController.php
require_once __DIR__ . '/AdminController.php';

class AdminGuideController extends AdminController {

    private $guideModel;
    private $categoryModel; // Добавляем модель категорий

    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    public function __construct() {
        parent::__construct();
        $this->guideModel = new Guide();
        $this->categoryModel = new Category(); // Создаем экземпляр
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---

    /**
     * Показывает список всех гайдов.
     */
    public function index() {
        $guides = $this->guideModel->getAll();
        $this->renderAdminPage('admin/guides/index', [
            'title' => 'Управление гайдами',
            'guides' => $guides
        ]);
    }

    /**
     * Показывает форму для создания нового гайда.
     */
    public function new() {
        // --- НАЧАЛО ИЗМЕНЕНИЙ ---
        $categories = $this->categoryModel->getAll();
        $this->renderAdminPage('admin/guides/new', [
            'title' => 'Новый гайд',
            'categories' => $categories
        ]);
        // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    }

    /**
     * Обрабатывает создание нового гайда.
     */
    public function create() {
        // --- НАЧАЛО ИЗМЕНЕНИЙ ---
        $title = $_POST['title'] ?? '';
        $slug = $this->generateSlug($title);
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';
        $content_text = $_POST['content_text'] ?? null;
        $content_url = $_POST['content_url'] ?? null;

        if (!empty($title) && !empty($slug)) {
            $guideId = $this->guideModel->create($title, $slug, $difficulty_level, $content_text, $content_url);
            if ($guideId) {
                $categoryIds = $_POST['category_ids'] ?? [];
                $this->guideModel->syncCategories($guideId, $categoryIds);
            }
        }

        header('Location: /admin/guides');
        exit();
        // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    }

    /**
     * Показывает форму редактирования гайда.
     */
    public function edit($id) {
        // --- НАЧАЛО ИЗМЕНЕНИЙ ---
        $guide = $this->guideModel->findById($id);
        if (!$guide) {
            http_response_code(404);
            die('Гайд не найден');
        }

        $categories = $this->categoryModel->getAll();
        $guideCategoryIds = $this->guideModel->getCategoryIdsForGuide($id);

        $this->renderAdminPage('admin/guides/edit', [
            'title' => 'Редактировать гайд',
            'guide' => $guide,
            'categories' => $categories,
            'guideCategoryIds' => $guideCategoryIds
        ]);
        // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    }

    /**
     * Обрабатывает обновление гайда.
     */
    public function update($id) {
        // --- НАЧАЛО ИЗМЕНЕНИЙ ---
        $title = $_POST['title'] ?? '';
        $slug = $this->generateSlug($title);
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';
        $content_text = $_POST['content_text'] ?? null;
        $content_url = $_POST['content_url'] ?? null;

        if (!empty($title) && !empty($slug)) {
            $this->guideModel->update($id, $title, $slug, $difficulty_level, $content_text, $content_url);
            $categoryIds = $_POST['category_ids'] ?? [];
            $this->guideModel->syncCategories($id, $categoryIds);
        }

        header('Location: /admin/guides');
        exit();
        // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    }

    /**
     * Обрабатывает удаление гайда.
     */
    public function delete($id) {
        $this->guideModel->delete($id);
        header('Location: /admin/guides');
        exit();
    }

    /**
     * Приватная функция для генерации слага из заголовка.
     * @param string $string
     * @return string
     */
    private function generateSlug($string) {
        $converter = [
            'а' => 'a',   'б' => 'b',   'в' => 'v',   'г' => 'g',   'д' => 'd',
            'е' => 'e',   'ё' => 'e',   'ж' => 'zh',  'з' => 'z',   'и' => 'i',
            'й' => 'y',   'к' => 'k',   'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',   'с' => 's',   'т' => 't',
            'у' => 'u',   'ф' => 'f',   'х' => 'h',   'ц' => 'c',   'ч' => 'ch',
            'ш' => 'sh',  'щ' => 'sch', 'ь' => '',    'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        ];

        $string = mb_strtolower($string);
        $string = strtr($string, $converter);
        $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }
}