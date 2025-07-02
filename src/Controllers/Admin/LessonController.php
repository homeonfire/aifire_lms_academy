<?php
// src/Controllers/Admin/LessonController.php
require_once __DIR__ . '/AdminController.php';

class AdminLessonController extends AdminController {

    private $lessonModel;
    private $homeworkModel;

    public function __construct() {
        parent::__construct();
        $this->lessonModel = new Lesson();
        $this->homeworkModel = new Homework();
    }

    /**
     * Обрабатывает создание нового урока
     */
    public function create() {
        $courseId = $_POST['course_id'] ?? null;
        $moduleId = $_POST['module_id'] ?? null;
        $title = $_POST['title'] ?? '';

        // Тип контента нам больше не нужен
        // $contentType = $_POST['content_type'] ?? 'video';

        if (empty($title) || empty($moduleId) || empty($courseId)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // ИСПРАВЛЕНО: Убран третий параметр
        $this->lessonModel->create($moduleId, $title);

        header('Location: /admin/courses/content/' . $courseId);
        exit();
    }

    // ... (остальные методы класса без изменений) ...

    public function update() {
        $courseId = $_POST['course_id'] ?? null;
        $lessonId = $_POST['lesson_id'] ?? null;
        $title = $_POST['title'] ?? '';

        if (!empty($title) && !empty($lessonId)) {
            $this->lessonModel->update($lessonId, $title);
        }

        header('Location: /admin/courses/content/' . $courseId);
        exit();
    }

    public function delete($id, $courseId) {
        $this->lessonModel->delete($id);
        header('Location: /admin/courses/content/' . $courseId);
        exit();
    }

    public function editContent($id) {
        $lesson = $this->lessonModel->findById($id);
        $homework = $this->homeworkModel->findByLessonId($id);

        if (!$lesson) {
            http_response_code(404); die('Урок не найден');
        }

        $this->render('admin/lessons/edit-content', [
            'title' => 'Редактировать контент урока',
            'lesson' => $lesson,
            'homework' => $homework
        ]);
    }

    public function saveContent($id) {
        $courseId = $_POST['course_id'] ?? null;
        $contentUrl = $_POST['content_url'] ?? null;
        $contentText = $_POST['content_text'] ?? null;

        $homeworkQuestions = $_POST['homework_questions'] ?? [];
        $filteredQuestions = array_filter($homeworkQuestions, function($q) {
            return !empty(trim($q));
        });
        $questionsForJson = array_map(function($q) {
            return ['q' => $q];
        }, $filteredQuestions);

        if (!empty($questionsForJson)) {
            $this->homeworkModel->createOrUpdate($id, json_encode($questionsForJson));
        } else {
            // Если вопросы пустые, можно добавить логику удаления ДЗ
        }

        $this->lessonModel->updateContent($id, $contentUrl, $contentText);

        header('Location: /admin/courses/content/' . $courseId);
        exit();
    }
}