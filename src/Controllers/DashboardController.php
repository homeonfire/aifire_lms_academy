<?php
// src/Controllers/DashboardController.php

class DashboardController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user'])) { // ИСПРАВЛЕНО
            header('Location: /login');
            exit();
        }
    }

    public function index() {
        // Подключаем модель курсов
        $courseModel = new Course();

        // Получаем данные из модели
        $featuredCourse = $courseModel->getFeaturedCourse();
        $latestCourses = $courseModel->getLatest(5); // Получаем 5 последних курсов

        // Передаем данные в шаблон
        $data = [
            'title' => 'Дашборд',
            'featuredCourse' => $featuredCourse,
            'latestCourses' => $latestCourses
        ];

        $this->render('dashboard/index', $data);
    }
}