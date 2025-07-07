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

        // 2. Добавляем информацию о доступе к каждому курсу
        $userId = $_SESSION['user']['id'];
        foreach ($courses as &$course) {
            $course['hasAccess'] = $this->courseModel->hasAccess($userId, $course['id']);
        }

        // 3. Остальная логика остается без изменений
        $favoriteModel = new Favorite();
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

        // Проверяем доступ к курсу
        $hasAccess = $this->courseModel->hasAccess($_SESSION['user']['id'], $courseId);
        $isFree = !empty($course['is_free']) || $course['price'] == 0;
        
        // Если курс платный и у пользователя нет доступа - перенаправляем на лендинг
        if (!$isFree && !$hasAccess) {
            $landingUrl = ($type === 'masterclass') ? "/masterclass/{$courseId}/landing" : "/course/{$courseId}/landing";
            header('Location: ' . $landingUrl);
            exit();
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

        // Проверяем доступ к курсу
        $hasAccess = $this->courseModel->hasAccess($_SESSION['user']['id'], $courseId);
        
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
            'type' => $type, // Передаем тип в шаблон
            'hasAccess' => $hasAccess // Добавляем информацию о доступе
        ];

        $this->render('courses/show', $data);
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---

    /**
     * Отображает страницу лендинга для курса.
     * Вызывается напрямую из роутера.
     *
     * @param int $courseId ID курса, полученный из URL.
     */
    public function landing($courseId)
    {
        if (empty($courseId)) {
            http_response_code(404);
            echo "<h1>404 Страница не найдена (ID курса не указан)</h1>";
            return;
        }

        $courseModel = new Course();
        // ИСПОЛЬЗУЕМ ПРАВИЛЬНЫЙ МЕТОД: findById()
        $course = $courseModel->findById($courseId);

        if (!$course) {
            http_response_code(404);
            echo "<h1>404 Страница не найдена (Курс не найден)</h1>";
            return;
        }

        $this->render('courses/course_landing', [
            'course' => $course,
            'layout' => 'clear'
        ]);
    }
}