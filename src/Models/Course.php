<?php
// src/Models/Course.php

class Course {

    private $pdo;

    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---


    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    /**
     * Получает несколько последних элементов (курсов или мастер-классов) с их категориями
     * @param string $type Тип элемента ('course' или 'masterclass')
     * @param int $limit Количество элементов для получения
     * @return array
     */
    public function getLatest($type = 'course', $limit = 5) {
        $sql = "SELECT c.*, GROUP_CONCAT(cat.name) as categories
            FROM courses c
            LEFT JOIN course_categories cc ON c.id = cc.course_id
            LEFT JOIN categories cat ON cc.category_id = cat.id
            WHERE c.type = :type
            GROUP BY c.id
            ORDER BY c.created_at DESC
            LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---


    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function getFeaturedCourse() {
        // Пока что просто берем самый первый курс для примера
        $stmt = $this->pdo->prepare("SELECT * FROM courses ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

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
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---


    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    /**
     * Получает все элементы указанного типа с их категориями
     * @param string $type Тип элемента ('course' или 'masterclass')
     * @return array
     */
    public function getAll($type = 'course') {
        $sql = "SELECT c.*, GROUP_CONCAT(cat.name) as categories
            FROM courses c
            LEFT JOIN course_categories cc ON c.id = cc.course_id
            LEFT JOIN categories cat ON cc.category_id = cat.id
            WHERE c.type = :type
            GROUP BY c.id
            ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Создает новый курс или мастер-класс
     * @param string $title
     * @param string $description
     * @param string $difficulty_level
     * @param string $type
     * @return string|false
     */
    public function create($title, $description, $difficulty_level, $type = 'course') {
        $stmt = $this->pdo->prepare("INSERT INTO courses (type, title, description, difficulty_level) VALUES (?, ?, ?, ?)");
        $stmt->execute([$type, $title, $description, $difficulty_level]);
        return $this->pdo->lastInsertId();
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---


    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---


    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    /**
     * Обновляет существующий курс или мастер-класс
     * @param int $id
     * @param string $title
     * @param string $description
     * @param string $difficulty_level
     * @return bool
     */
    public function update($id, $title, $description, $difficulty_level) {
        // Мы не меняем тип при редактировании, только другие данные
        $stmt = $this->pdo->prepare("UPDATE courses SET title = ?, description = ?, difficulty_level = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $difficulty_level, $id]);
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---


    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function countLessons($courseId) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(l.id) FROM lessons l
         JOIN modules m ON l.module_id = m.id
         WHERE m.course_id = ?"
        );
        $stmt->execute([$courseId]);
        return (int) $stmt->fetchColumn();
    }

    public function getCategoryIdsForCourse($courseId) {
        $stmt = $this->pdo->prepare("SELECT category_id FROM course_categories WHERE course_id = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function syncCategories($courseId, $categoryIds) {
        $stmtDelete = $this->pdo->prepare("DELETE FROM course_categories WHERE course_id = ?");
        $stmtDelete->execute([$courseId]);

        if (!empty($categoryIds)) {
            $stmtInsert = $this->pdo->prepare("INSERT INTO course_categories (course_id, category_id) VALUES (?, ?)");
            foreach ($categoryIds as $categoryId) {
                $stmtInsert->execute([$courseId, $categoryId]);
            }
        }
    }
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---
}