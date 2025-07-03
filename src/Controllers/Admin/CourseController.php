<?php
// src/Controllers/Admin/CourseController.php

class AdminCourseController extends AdminController {

    private $courseModel;
    private $categoryModel;

    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function __construct() {
        parent::__construct();
        $this->courseModel = new Course();
        $this->categoryModel = new Category();
    }
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---

    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    /**
     * Показывает список всех курсов или мастер-классов
     */
    public function index() {
        // Определяем, какой тип контента показывать, по URL
        $type = (strpos($_SERVER['REQUEST_URI'], 'masterclasses') !== false) ? 'masterclass' : 'course';
        $pageTitle = ($type === 'masterclass') ? 'Управление мастер-классами' : 'Управление курсами';

        $courses = $this->courseModel->getAll($type);
        $this->renderAdminPage('admin/courses/index', [
            'title' => $pageTitle,
            'courses' => $courses,
            'type' => $type // Передаем тип в шаблон для правильных ссылок
        ]);
    }

    /**
     * Показывает форму для создания нового элемента
     */
    public function new() {
        $type = (strpos($_SERVER['REQUEST_URI'], 'masterclasses') !== false) ? 'masterclass' : 'course';
        $pageTitle = ($type === 'masterclass') ? 'Новый мастер-класс' : 'Новый курс';

        $categories = $this->categoryModel->getAll();
        $this->renderAdminPage('admin/courses/new', [
            'title' => $pageTitle,
            'categories' => $categories,
            'type' => $type
        ]);
    }

    /**
     * Обрабатывает создание нового элемента
     */
    public function create() {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';
        $type = $_POST['type'] ?? 'course'; // Получаем тип из скрытого поля формы

        if (empty($title)) {
            // ... обработка ошибки
        }

        $lastInsertId = $this->courseModel->create($title, $description, $difficulty_level, $type);

        if ($lastInsertId) {
            $categoryIds = $_POST['category_ids'] ?? [];
            $this->courseModel->syncCategories($lastInsertId, $categoryIds);
        }

        // Редирект на правильную страницу списка
        $redirectUrl = ($type === 'masterclass') ? '/admin/masterclasses' : '/admin/courses';
        header('Location: ' . $redirectUrl);
        exit();
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---


    // --- БЕЗ ИЗМЕНЕНИЙ (остальные методы) ---
    public function content($id) {
        $course = $this->courseModel->getCourseWithModulesAndLessons($id);
        if (!$course) {
            http_response_code(404);
            die('Элемент не найден');
        }
        $this->renderAdminPage('admin/courses/content', [
            'title' => 'Управление контентом',
            'course' => $course
        ]);
    }

    public function edit($id) {
        $course = $this->courseModel->findById($id);
        if (!$course) { http_response_code(404); die('Элемент не найден'); }

        $pageTitle = ($course['type'] === 'masterclass') ? 'Редактировать мастер-класс' : 'Редактировать курс';
        $categories = $this->categoryModel->getAll();
        $courseCategoryIds = $this->courseModel->getCategoryIdsForCourse($id);

        $this->renderAdminPage('admin/courses/edit', [
            'title' => $pageTitle,
            'course' => $course,
            'categories' => $categories,
            'courseCategoryIds' => $courseCategoryIds
        ]);
    }

    public function update($id) {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';

        if (empty($title)) {
            header('Location: /admin/courses/edit/' . $id); // Редирект на редактирование
            exit();
        }

        $this->courseModel->update($id, $title, $description, $difficulty_level);
        $categoryIds = $_POST['category_ids'] ?? [];
        $this->courseModel->syncCategories($id, $categoryIds);

        // Определяем, куда редиректить после обновления
        $course = $this->courseModel->findById($id);
        $redirectUrl = ($course['type'] === 'masterclass') ? '/admin/masterclasses' : '/admin/courses';
        header('Location: ' . $redirectUrl);
        exit();
    }

    public function delete($id) {
        // Перед удалением узнаем тип, чтобы правильно редиректить
        $course = $this->courseModel->findById($id);
        $redirectUrl = ($course && $course['type'] === 'masterclass') ? '/admin/masterclasses' : '/admin/courses';

        $this->courseModel->delete($id);
        header('Location: ' . $redirectUrl);
        exit();
    }
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---
}