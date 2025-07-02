<?php
// src/Controllers/CourseController.php

class CourseController extends Controller {

    private $courseModel;

    public function __construct() {
        // Проверяем авторизацию
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }
        // Подключаем модель
        require_once __DIR__ . '/../Models/Course.php';
        $this->courseModel = new Course();
    }

    /**
     * Показывает страницу одного курса
     * @param int $id ID курса
     */
    public function show($courseId, $lessonId = null) {
        $course = $this->courseModel->getCourseWithModulesAndLessons($courseId);

        if (!$course) {
            http_response_code(404);
            echo "<h1>404 Курс не найден</h1>";
            return;
        }

        // --- НАЧАЛО НОВОЙ ЛОГИКИ ВЫБОРА УРОКА ---
        $activeLesson = null;

        if ($lessonId === null) {
            // Если ID урока не указан, берем самый первый
            if (!empty($course['modules']) && !empty($course['modules'][0]['lessons'])) {
                $activeLesson = $course['modules'][0]['lessons'][0];
            }
        } else {
            // Если ID урока указан, ищем его в структуре курса
            foreach ($course['modules'] as $module) {
                foreach ($module['lessons'] as $lesson) {
                    if ($lesson['id'] == $lessonId) {
                        $activeLesson = $lesson;
                        break 2; // Выходим из обоих циклов, как только нашли
                    }
                }
            }
            // Если урок с таким ID не найден в этом курсе, это ошибка
            if ($activeLesson === null) {
                http_response_code(404);
                echo "<h1>404 Урок не найден в данном курсе</h1>";
                return;
            }
        }
        // --- КОНЕЦ НОВОЙ ЛОГИКИ ---

        $data = [
            'title' => $course['title'],
            'course' => $course,
            'activeLesson' => $activeLesson
        ];

        $this->render('courses/show', $data);
    }

    /**
     * Показывает страницу каталога всех курсов
     */
    public function index() {
        $courses = $this->courseModel->getAll();

        $data = [
            'title' => 'Все курсы',
            'courses' => $courses
        ];

        $this->render('courses/index', $data);
    }
}