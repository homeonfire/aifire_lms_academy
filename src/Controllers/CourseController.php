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
        // 1. Указываем, что хотим получить именно 'course'
        $courses = $this->courseModel->getAll('course');

        // 2. Остальная логика остается без изменений
        $favoriteModel = new Favorite();
        $userId = $_SESSION['user']['id'];
        $favoritedCoursesRaw = $favoriteModel->getFavoritedCourses($userId);
        $favoritedCourseIds = array_column($favoritedCoursesRaw, 'id');

        $data = [
            'title' => 'Все курсы',
            'courses' => $courses,
            'favoritedCourseIds' => $favoritedCourseIds,
            'type' => 'course'
        ];
        $this->render('courses/index', $data);
    }

    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    /**
     * Показывает страницу курса или мастер-класса
     * @param int $courseId
     * @param int|null $lessonId
     * @param string $type
     */
    public function show($courseId, $lessonId = null, $type = 'course') {
        $course = $this->courseModel->getCourseWithModulesAndLessons($courseId);
        if (!$course) {
            http_response_code(404);
            echo "<h1>404 Элемент не найден</h1>";
            return;
        }

        // Защита: чтобы нельзя было открыть курс по ссылке /masterclass/ и наоборот
        if ($course['type'] !== $type) {
            http_response_code(404);
            echo "<h1>404 Запрашиваемый ресурс не найден</h1>";
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

        $favoriteModel = new Favorite();
        $userId = $_SESSION['user']['id'];
        $isCourseFavorited = $favoriteModel->isFavorite($userId, $courseId, $type);
        $favoritedLessonsRaw = $favoriteModel->getFavoritedLessons($userId);
        $favoritedLessonIds = array_column($favoritedLessonsRaw, 'id');

        $data = [
            'title' => $course['title'],
            'course' => $course,
            'activeLesson' => $activeLesson,
            'homework' => $homework,
            'userAnswer' => $userAnswer,
            'progressPercentage' => $progressPercentage,
            'completedLessonIds' => $completedLessonIds,
            'favoritedLessonIds' => $favoritedLessonIds,
            'isCourseFavorited' => $isCourseFavorited,
            'type' => $type // Передаем тип в шаблон
        ];

        $this->render('courses/show', $data);
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---
}