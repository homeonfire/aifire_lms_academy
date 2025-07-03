<?php
// src/Controllers/Admin/GuideController.php


class AdminGuideController extends AdminController {

    private $guideModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->guideModel = new \Guide(); // Используем \Guide() для глобального пространства имен
        $this->categoryModel = new \Category(); // Используем \Category() для глобального пространства имен
    }

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
        $categories = $this->categoryModel->getAll();
        $this->renderAdminPage('admin/guides/new', [
            'title' => 'Новый гайд',
            'categories' => $categories
        ]);
    }

    /**
     * Обрабатывает создание нового гайда.
     */
    public function create() {
        $title = $_POST['title'] ?? '';
        $slug = $this->generateSlug($title);
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';
        // ИЗМЕНЕНО: content_text УДАЛЕН, используем только content_url
        $content_url = $_POST['content_url'] ?? null;
        $contentJson = $_POST['content_json'] ?? null; // получаем content_json

        if (empty($title)) {
            $_SESSION['error_message'] = 'Название гайда обязательно.';
            header('Location: /admin/guides/new');
            exit();
        }

        // Если contentJson должен быть обязательным, добавьте валидацию здесь
        // if (empty($contentJson) || json_decode($contentJson) === null) {
        //     $_SESSION['error_message'] = 'Контент гайда обязателен и должен быть в правильном формате.';
        //     header('Location: /admin/guides/new');
        //     exit();
        // }

        // ИЗМЕНЕНО: Передаем contentJson, content_text УДАЛЕН
        $guideId = $this->guideModel->create($title, $slug, $difficulty_level, $content_url, $contentJson);
        if ($guideId) {
            $categoryIds = $_POST['category_ids'] ?? [];
            $this->guideModel->syncCategories($guideId, $categoryIds);
            $_SESSION['success_message'] = 'Гайд "' . htmlspecialchars($title) . '" успешно создан!';
        } else {
            $_SESSION['error_message'] = 'Ошибка при создании гайда.';
        }

        header('Location: /admin/guides');
        exit();
    }

    /**
     * Показывает форму редактирования гайда.
     */
    public function edit($id) {
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
    }

    /**
     * Обрабатывает обновление гайда.
     */
    public function update($id) {
        $title = $_POST['title'] ?? '';
        $slug = $this->generateSlug($title);
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';
        // ИЗМЕНЕНО: content_text УДАЛЕН, используем только content_url
        $content_url = $_POST['content_url'] ?? null;
        $contentJson = $_POST['content_json'] ?? null; // получаем content_json

        if (empty($title)) {
            $_SESSION['error_message'] = 'Название гайда обязательно.';
            header('Location: /admin/guides/edit/' . $id);
            exit();
        }

        // Если contentJson должен быть обязательным, добавьте валидацию здесь
        // if (empty($contentJson) || json_decode($contentJson) === null) {
        //     $_SESSION['error_message'] = 'Контент гайда обязателен и должен быть в правильном формате.';
        //     header('Location: /admin/guides/edit/' . $id);
        //     exit();
        // }

        // ИЗМЕНЕНО: Передаем contentJson, content_text УДАЛЕН
        if ($this->guideModel->update($id, $title, $slug, $difficulty_level, $content_url, $contentJson)) {
            $categoryIds = $_POST['category_ids'] ?? [];
            $this->guideModel->syncCategories($id, $categoryIds);
            $_SESSION['success_message'] = 'Гайд "' . htmlspecialchars($title) . '" успешно обновлен!';
        } else {
            $_SESSION['error_message'] = 'Ошибка при обновлении гайда.';
        }

        header('Location: /admin/guides');
        exit();
    }

    /**
     * Обрабатывает удаление гайда.
     */
    public function delete($id) {
        if ($this->guideModel->delete($id)) {
            $_SESSION['success_message'] = 'Гайд успешно удален.';
        } else {
            $_SESSION['error_message'] = 'Ошибка при удалении гайда.';
        }
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