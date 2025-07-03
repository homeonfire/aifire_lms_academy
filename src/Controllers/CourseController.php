<?php
// src/Controllers/CourseController.php

class CourseController extends Controller {

    private $courseModel;

    public function __construct() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit();
        }
        $this->courseModel = new Course();
    }

    public function index() {
        $courses = $this->courseModel->getAll();

        // --- Добавляем логику избранного ---
        $favoriteModel = new Favorite();
        $userId = $_SESSION['user']['id'];
        $favoritedCoursesRaw = $favoriteModel->getFavoritedCourses($userId);
        $favoritedCourseIds = array_column($favoritedCoursesRaw, 'id');
        // --- Конец логики ---

        $data = [
            'title' => 'Все курсы',
            'courses' => $courses,
            'favoritedCourseIds' => $favoritedCourseIds // Передаем ID в шаблон
        ];
        $this->render('courses/index', $data);
    }

    public function show($courseId, $lessonId = null) {
        $course = $this->courseModel->getCourseWithModulesAndLessons($courseId);
        if (!$course) {
            http_response_code(404);
            echo "<h1>404 Курс не найден</h1>";
            return;
        }

        $activeLesson = null;
        if ($lessonId === null) {
            if (!empty($course['modules']) && !empty($course['modules'][0]['lessons'])) {
                $activeLesson = $course['modules'][0]['lessons'][0];
            }
        } else {
            foreach ($course['modules'] as $module) {
                foreach ($module['lessons'] as $lesson) {
                    if ($lesson['id'] == $lessonId) {
                        $activeLesson = $lesson;
                        break 2;
                    }
                }
            }
            if ($activeLesson === null) {
                http_response_code(404);
                echo "<h1>404 Урок не найден в данном курсе</h1>";
                return;
            }
        }

        $homework = null;
        $userAnswer = null;
        if ($activeLesson) {
            $homeworkModel = new Homework();
            $homeworkAnswerModel = new HomeworkAnswer();
            $homework = $homeworkModel->findByLessonId($activeLesson['id']);
            if ($homework) {
                $userAnswer = $homeworkAnswerModel->findByUserAndHomework($_SESSION['user']['id'], $homework['id']);
            }
        }

        $progressModel = new LessonProgress();
        $totalLessons = $this->courseModel->countLessons($courseId);
        $completedLessonIds = $progressModel->getCompletedLessonIdsForUser($_SESSION['user']['id'], $courseId);
        $completedCount = count($completedLessonIds);
        $progressPercentage = ($totalLessons > 0) ? round(($completedCount / $totalLessons) * 100) : 0;

        // --- ДОБАВЛЯЕМ ЛОГИКУ ИЗБРАННОГО ---
        $favoriteModel = new Favorite();
        $favoritedLessonsRaw = $favoriteModel->getFavoritedLessons($_SESSION['user']['id']);
        $favoritedLessonIds = array_column($favoritedLessonsRaw, 'id');
        // --- КОНЕЦ ЛОГИКИ ---

        $data = [
            'title' => $course['title'],
            'course' => $course,
            'activeLesson' => $activeLesson,
            'homework' => $homework,
            'userAnswer' => $userAnswer,
            'progressPercentage' => $progressPercentage,
            'completedLessonIds' => $completedLessonIds,
            'favoritedLessonIds' => $favoritedLessonIds // <-- Добавляем новую переменную
        ];

        $this->render('courses/show', $data);
    }
}