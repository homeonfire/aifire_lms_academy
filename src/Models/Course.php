<?php
// src/Models/Course.php

class Course {

    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    /**
     * Получает несколько последних курсов с их категориями
     * @param int $limit Количество курсов для получения
     * @return array
     */
    public function getLatest($limit = 5) {
        $sql = "SELECT c.*, GROUP_CONCAT(cat.name) as categories
            FROM courses c
            LEFT JOIN course_categories cc ON c.id = cc.course_id
            LEFT JOIN categories cat ON cc.category_id = cat.id
            GROUP BY c.id
            ORDER BY c.created_at DESC 
            LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
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
    /**
     * Получает все курсы с их категориями
     * @return array
     */
    public function getAll() {
        $sql = "SELECT c.*, GROUP_CONCAT(cat.name) as categories
            FROM courses c
            LEFT JOIN course_categories cc ON c.id = cc.course_id
            LEFT JOIN categories cat ON cc.category_id = cat.id
            GROUP BY c.id
            ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Создает новый курс
     * @param string $title
     * @param string $description
     * @return bool
     */
    public function create($title, $description, $difficulty_level) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (title, description, difficulty_level) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $difficulty_level]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Удаляет курс по его ID
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Обновляет существующий курс
     * @param int $id
     * @param string $title
     * @param string $description
     * @return bool
     */
    public function update($id, $title, $description, $difficulty_level) {
        $stmt = $this->pdo->prepare("UPDATE courses SET title = ?, description = ?, difficulty_level = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $difficulty_level, $id]);
    }

    public function countLessons($courseId) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(l.id) FROM lessons l
         JOIN modules m ON l.module_id = m.id
         WHERE m.course_id = ?"
        );
        $stmt->execute([$courseId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Получает массив ID категорий для конкретного курса
     * @param int $courseId
     * @return array
     */
    public function getCategoryIdsForCourse($courseId) {
        $stmt = $this->pdo->prepare("SELECT category_id FROM course_categories WHERE course_id = ?");
        $stmt->execute([$courseId]);
        // Возвращаем простой массив ID, например [1, 5, 8]
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /**
     * Синхронизирует категории для курса (удаляет старые, добавляет новые)
     * @param int $courseId
     * @param array $categoryIds
     */
    public function syncCategories($courseId, $categoryIds) {
        // 1. Удаляем все старые привязки для этого курса
        $stmtDelete = $this->pdo->prepare("DELETE FROM course_categories WHERE course_id = ?");
        $stmtDelete->execute([$courseId]);

        // 2. Добавляем новые привязки
        if (!empty($categoryIds)) {
            $stmtInsert = $this->pdo->prepare("INSERT INTO course_categories (course_id, category_id) VALUES (?, ?)");
            foreach ($categoryIds as $categoryId) {
                $stmtInsert->execute([$courseId, $categoryId]);
            }
        }
    }
}