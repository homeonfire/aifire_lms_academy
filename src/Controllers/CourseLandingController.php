<?php
// src/Controllers/CourseLandingController.php

class CourseLandingController extends Controller {

    private $courseModel;

    public function __construct() {
        $this->courseModel = new Course();
    }

    /**
     * Показывает лендинг курса
     * @param int $id ID курса
     */
    public function show($id) {
        // Отладочная информация
        file_put_contents(__DIR__ . '/../../test_router.log', "LANDING CONTROLLER SHOW id=$id " . date('c') . "\n", FILE_APPEND);
        
        // Определяем тип контента из URL
        $type = (strpos($_SERVER['REQUEST_URI'], 'masterclass') !== false) ? 'masterclass' : 'course';
        
        // Получаем курс с модулями и уроками
        $course = $this->courseModel->getCourseWithModulesAndLessons($id);
        
        if (!$course) {
            http_response_code(404);
            die('Курс не найден');
        }

        // Проверяем, что тип курса соответствует URL
        if ($course['type'] !== $type) {
            http_response_code(404);
            die('Запрашиваемый ресурс не найден');
        }

        // Подсчитываем общее количество уроков
        $totalLessons = 0;
        if (!empty($course['modules'])) {
            foreach ($course['modules'] as $module) {
                $totalLessons += count($module['lessons']);
            }
        }

        // Проверяем доступ пользователя к курсу
        $hasAccess = false;
        if (isset($_SESSION['user'])) {
            $hasAccess = $this->courseModel->hasAccess($_SESSION['user']['id'], $id);
        }

        // Получаем категории курса
        $courseCategoryIds = $this->courseModel->getCategoryIdsForCourse($id);
        $categoryModel = new Category();
        $categories = [];
        if (!empty($courseCategoryIds)) {
            foreach ($courseCategoryIds as $categoryId) {
                $category = $categoryModel->findById($categoryId);
                if ($category) {
                    $categories[] = $category['name'];
                }
            }
        }
        $course['categories'] = implode(', ', $categories);

        error_log("CourseLandingController::show - about to render with course: " . json_encode($course['title']));
        
        $this->render('courses/landing', [
            'course' => $course,
            'totalLessons' => $totalLessons,
            'hasAccess' => $hasAccess,
            'type' => $type
        ]);
        
        error_log("CourseLandingController::show - render completed");
    }
} 