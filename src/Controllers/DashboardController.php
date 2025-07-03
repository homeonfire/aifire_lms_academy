<?php
// src/Controllers/DashboardController.php

class DashboardController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
    }

    public function index() {
        $courseModel = new Course();
        $favoriteModel = new Favorite(); // Создаем экземпляр новой модели
        $userId = $_SESSION['user']['id'];

        // Получаем ID всех курсов в избранном
        $favoritedCoursesRaw = $favoriteModel->getFavoritedCourses($userId);
        $favoritedCourseIds = array_column($favoritedCoursesRaw, 'id');

        $featuredCourse = $courseModel->getFeaturedCourse();
        $latestCourses = $courseModel->getLatest(5);

        $data = [
            'title' => 'Дашборд',
            'featuredCourse' => $featuredCourse,
            'latestCourses' => $latestCourses,
            'favoritedCourseIds' => $favoritedCourseIds // Передаем ID в шаблон
        ];

        $this->render('dashboard/index', $data);
    }
}