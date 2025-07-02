<?php
// src/Controllers/Admin/CourseController.php

class AdminCourseController extends AdminController {

    private $courseModel;
    private $categoryModel; // Объявляем новое свойство

    public function __construct() {
        parent::__construct();
        $this->courseModel = new Course();
        $this->categoryModel = new Category(); // Создаем экземпляр модели категорий
    }

    /**
     * Показывает список всех курсов
     */
    public function index() {
        $courses = $this->courseModel->getAll();
        $this->renderAdminPage('admin/courses/index', [
            'title' => 'Управление курсами',
            'courses' => $courses
        ]);
    }

    /**
     * Показывает форму для создания нового курса
     */
    public function new() {
        // Получаем все категории для отображения в форме
        $categories = $this->categoryModel->getAll();
        $this->renderAdminPage('admin/courses/new', [
            'title' => 'Новый курс',
            'categories' => $categories // Передаем категории в шаблон
        ]);
    }

    /**
     * Обрабатывает создание нового курса
     */
    public function create() {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';

        if (empty($title)) {
            $this->renderAdminPage('admin/courses/new', [
                'title' => 'Новый курс',
                'error' => 'Название не может быть пустым.',
                'categories' => $this->categoryModel->getAll() // Не забываем передать категории снова в случае ошибки
            ]);
            return;
        }

        $lastInsertId = $this->courseModel->create($title, $description, $difficulty_level);

        if ($lastInsertId) {
            $categoryIds = $_POST['category_ids'] ?? [];
            $this->courseModel->syncCategories($lastInsertId, $categoryIds);
        }

        header('Location: /admin/courses');
        exit();
    }

    /**
     * Показывает страницу управления контентом курса (модули и уроки)
     */
    public function content($id) {
        $course = $this->courseModel->getCourseWithModulesAndLessons($id);

        if (!$course) {
            http_response_code(404);
            die('Курс не найден');
        }

        $this->renderAdminPage('admin/courses/content', [
            'title' => 'Управление контентом',
            'course' => $course
        ]);
    }

    /**
     * Показывает форму для редактирования курса
     */
    public function edit($id) {
        $course = $this->courseModel->findById($id);
        if (!$course) { http_response_code(404); die('Курс не найден'); }

        $categories = $this->categoryModel->getAll();
        // Получаем ID уже отмеченных категорий
        $courseCategoryIds = $this->courseModel->getCategoryIdsForCourse($id);

        $this->renderAdminPage('admin/courses/edit', [
            'title' => 'Редактировать курс',
            'course' => $course,
            'categories' => $categories,
            'courseCategoryIds' => $courseCategoryIds // Передаем в шаблон
        ]);
    }

    /**
     * Обрабатывает обновление курса
     */
    public function update($id) {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';

        if (empty($title)) {
            header('Location: /admin/courses/edit/' . $id);
            exit();
        }

        $this->courseModel->update($id, $title, $description, $difficulty_level);

        // Обновляем категории
        $categoryIds = $_POST['category_ids'] ?? [];
        $this->courseModel->syncCategories($id, $categoryIds);

        header('Location: /admin/courses');
        exit();
    }

    /**
     * Обрабатывает удаление курса
     */
    public function delete($id) {
        $this->courseModel->delete($id);
        header('Location: /admin/courses');
        exit();
    }
}