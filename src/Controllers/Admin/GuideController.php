<?php
// src/Controllers/Admin/GuideController.php

class AdminGuideController extends AdminController {

    private $guideModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->guideModel = new Guide();
        $this->categoryModel = new Category();
    }

    public function index() {
        $guides = $this->guideModel->getAll();
        $this->renderAdminPage('admin/guides/index', [
            'title' => 'Управление гайдами',
            'guides' => $guides
        ]);
    }

    public function new() {
        $categories = $this->categoryModel->getAll();
        $this->renderAdminPage('admin/guides/new', [
            'title' => 'Новый гайд',
            'categories' => $categories
        ]);
    }

    public function create()
    {
        $coverUrl = $this->handleImageUpload($_FILES['cover_url'] ?? null);
        $slug = $this->generateSlug($_POST['title']);

        $guideData = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'difficulty_level' => $_POST['difficulty_level'],
            'content_url' => $_POST['content_url'],
            'content_json' => $_POST['content_json'],
            'cover_url' => $coverUrl
        ];

        $guideId = $this->guideModel->create($guideData);

        // ИСПРАВЛЕНО: Теперь мы проверяем $_POST['category_ids']
        if ($guideId && isset($_POST['category_ids'])) {
            $this->guideModel->syncCategories($guideId, $_POST['category_ids']);
        }

        header('Location: /admin/guides');
        exit();
    }

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

    public function update($id)
    {
        $currentGuide = $this->guideModel->findById($id);
        $currentCoverPath = $currentGuide['cover_url'] ?? null;
        $coverUrl = $this->handleImageUpload($_FILES['cover_url'] ?? null, $currentCoverPath);

        $slug = $this->generateSlug($_POST['title']);

        $updateData = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'difficulty_level' => $_POST['difficulty_level'],
            'content_url' => $_POST['content_url'],
            'content_json' => $_POST['content_json'],
            'cover_url' => $coverUrl
        ];

        $this->guideModel->update($id, $updateData);

        // ИСПРАВЛЕНО: Теперь мы проверяем $_POST['category_ids']
        if (isset($_POST['category_ids'])) {
            $this->guideModel->syncCategories($id, $_POST['category_ids']);
        } else {
            $this->guideModel->syncCategories($id, []);
        }

        header('Location: /admin/guides/edit/' . $id);
        exit();
    }

    public function delete($id) {
        if ($this->guideModel->delete($id)) {
            $_SESSION['success_message'] = 'Гайд успешно удален.';
        } else {
            $_SESSION['error_message'] = 'Ошибка при удалении гайда.';
        }
        header('Location: /admin/guides');
        exit();
    }

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

    private function handleImageUpload($file, $currentPath = null) {
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../../public/assets/uploads/guides/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if ($currentPath && file_exists(__DIR__ . '/../../../public' . $currentPath)) {
                unlink(__DIR__ . '/../../../public' . $currentPath);
            }
            $fileName = uniqid('guide_') . '-' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return '/public/assets/uploads/guides/' . $fileName;
            }
        }
        return $currentPath;
    }
}