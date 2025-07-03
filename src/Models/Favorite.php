<?php
// src/Models/Favorite.php

class Favorite {
    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../Config/database.php';
        $this->pdo = getDBConnection();
    }

    /**
     * Добавляет или удаляет элемент из избранного.
     * @param int $userId
     * @param int $itemId
     * @param string $itemType
     * @return string ('added' или 'removed')
     */
    public function toggle($userId, $itemId, $itemType) {
        if ($this->isFavorite($userId, $itemId, $itemType)) {
            // Если уже в избранном - удаляем
            $stmt = $this->pdo->prepare(
                "DELETE FROM user_favorites WHERE user_id = ? AND item_id = ? AND item_type = ?"
            );
            $stmt->execute([$userId, $itemId, $itemType]);
            return 'removed';
        } else {
            // Если нет - добавляем
            $stmt = $this->pdo->prepare(
                "INSERT INTO user_favorites (user_id, item_id, item_type) VALUES (?, ?, ?)"
            );
            $stmt->execute([$userId, $itemId, $itemType]);
            return 'added';
        }
    }

    /**
     * Проверяет, находится ли элемент в избранном.
     * @param int $userId
     * @param int $itemId
     * @param string $itemType
     * @return bool
     */
    public function isFavorite($userId, $itemId, $itemType) {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM user_favorites WHERE user_id = ? AND item_id = ? AND item_type = ?"
        );
        $stmt->execute([$userId, $itemId, $itemType]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Получает все избранные курсы для пользователя.
     * @param int $userId
     * @return array
     */
    public function getFavoritedCourses($userId) {
        $sql = "SELECT c.*, GROUP_CONCAT(cat.name) as categories
                FROM courses c
                JOIN user_favorites uf ON c.id = uf.item_id
                LEFT JOIN course_categories cc ON c.id = cc.course_id
                LEFT JOIN categories cat ON cc.category_id = cat.id
                WHERE uf.user_id = ? AND uf.item_type = 'course'
                GROUP BY c.id
                ORDER BY uf.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Получает все избранные уроки для пользователя.
     * @param int $userId
     * @return array
     */
    public function getFavoritedLessons($userId) {
        // Этот запрос получит уроки и информацию об их курсах
        $sql = "SELECT l.*, c.title as course_title, c.id as course_id
                FROM lessons l
                JOIN user_favorites uf ON l.id = uf.item_id
                JOIN modules m ON l.module_id = m.id
                JOIN courses c ON m.course_id = c.id
                WHERE uf.user_id = ? AND uf.item_type = 'lesson'
                ORDER BY uf.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}