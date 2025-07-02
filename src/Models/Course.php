<?php
// src/Models/Course.php

class Course {

    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../Config/database.php';
        $this->pdo = getDBConnection();
    }

    /**
     * Получает несколько последних курсов
     * @param int $limit Количество курсов для получения
     * @return array
     */
    public function getLatest($limit = 5) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses ORDER BY created_at DESC LIMIT :limit");
        // bindValue требует, чтобы limit был integer
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Получает один "главный" курс (в будущем по отметке is_featured)
     * @return mixed
     */
    public function getFeaturedCourse() {
        // Пока что просто берем самый первый курс для примера
        $stmt = $this->pdo->prepare("SELECT * FROM courses ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Находит один курс по его ID
     * @param int $id
     * @return mixed
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Получает курс со всеми его модулями и уроками
     * @param int $id ID курса
     * @return mixed
     */
    public function getCourseWithModulesAndLessons($id) {
        $course = $this->findById($id);
        if (!$course) {
            return false;
        }

        // Получаем модули
        $stmtModules = $this->pdo->prepare("SELECT * FROM modules WHERE course_id = ? ORDER BY order_number ASC");
        $stmtModules->execute([$id]);
        $modules = $stmtModules->fetchAll();

        // Для каждого модуля получаем его уроки
        $stmtLessons = $this->pdo->prepare("SELECT * FROM lessons WHERE module_id = ? ORDER BY order_number ASC");
        foreach ($modules as $key => $module) {
            $stmtLessons->execute([$module['id']]);
            $modules[$key]['lessons'] = $stmtLessons->fetchAll();
        }

        $course['modules'] = $modules;
        return $course;
    }

    /**
     * Получает все курсы
     * @return array
     */
    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM courses ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}