<?php
// src/Controllers/MasterclassController.php

class MasterclassController extends Controller {

    private $courseModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
        $this->courseModel = new Course();
    }

    /**
     * Показывает страницу со списком всех мастер-классов
     */
    public function index() {
        // --- НАЧАЛО ИЗМЕНЕНИЙ ---
        $masterclasses = $this->courseModel->getAll('masterclass');

        $favoriteModel = new Favorite();
        $userId = $_SESSION['user']['id'];
        $favoritedCoursesRaw = $favoriteModel->getFavoritedCourses($userId);
        $favoritedCourseIds = array_column($favoritedCoursesRaw, 'id');

        $data = [
            'title' => 'Все мастер-классы',
            'courses' => $masterclasses,
            'favoritedCourseIds' => $favoritedCourseIds,
            'type' => 'masterclass' // Явно передаем тип для заголовка и других нужд
        ];

        $this->render('courses/index', $data);
        // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    }

    /**
     * Показывает страницу конкретного мастер-класса
     * @param int $masterclassId
     * @param int|null $lessonId
     */
    public function show($masterclassId, $lessonId = null) {
        // --- НАЧАЛО ИЗМЕНЕНИЙ ---
        // Вызываем метод контроллера курсов, но явно передаем тип 'masterclass'
        (new CourseController())->show($masterclassId, $lessonId, 'masterclass');
        // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    }
}