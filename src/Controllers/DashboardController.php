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
        $favoriteModel = new Favorite();
        $guideModel = new Guide();
        $userId = $_SESSION['user']['id'];

        // --- НАЧАЛО ИЗМЕНЕНИЙ ---

        // 1. Получаем курсы, которые пользователь уже начал
        $startedCourses = $courseModel->findStartedForUser($userId);

        // 2. Получаем ID избранных для ВСЕХ типов контента
        $favoritedCoursesRaw = $favoriteModel->getFavoritedCourses($userId);
        $favoritedCourseIds = array_column($favoritedCoursesRaw, 'id');

        $favoritedGuidesRaw = $favoriteModel->getFavoritedGuides($userId);
        $favoritedGuideIds = array_column($favoritedGuidesRaw, 'id');

        // 3. Получаем последние элементы для остальных блоков
        $featuredCourse = $courseModel->getFeaturedCourse();
        $latestCourses = $courseModel->getLatest('course', 5);
        $latestMasterclasses = $courseModel->getLatest('masterclass', 5);
        $latestGuides = $guideModel->getLatest(5);

        $data = [
            'title' => 'Дашборд',
            'startedCourses' => $startedCourses, // <-- Передаем начатые курсы
            'featuredCourse' => $featuredCourse,
            'latestCourses' => $latestCourses,
            'latestMasterclasses' => $latestMasterclasses,
            'latestGuides' => $latestGuides,
            'favoritedCourseIds' => $favoritedCourseIds,
            'favoritedGuideIds' => $favoritedGuideIds
        ];

        $this->render('dashboard/index', $data);

        // --- КОНЕЦ ИЗМЕНЕНИЙ ---
    }
}