<?php
// src/Models/Guide.php

// namespace App\Models; // ЗАКОММЕНТИРУЙТЕ ИЛИ УДАЛИТЕ ЭТУ СТРОКУ, если вы используете глобальное пространство имен

class Guide { // Имя класса Guide (единственное число), как в вашем файле

    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    /**
     * Получает все гайды с их категориями.
     * @return array
     */
    public function getAll() {
        $sql = "SELECT g.*, GROUP_CONCAT(cat.name) as categories
                FROM guides g
                LEFT JOIN guide_categories gc ON g.id = gc.guide_id
                LEFT JOIN categories cat ON gc.category_id = cat.id
                GROUP BY g.id
                ORDER BY g.created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBySlug($slug) {
        $stmt = $this->pdo->prepare("SELECT * FROM guides WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM guides WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Создает новый гайд.
     * @param string $title
     * @param string $slug
     * @param string $difficulty_level
     * @param string $content_url
     * @param string|null $contentJson - контент Editor.js
     * @return int|false ID нового гайда или false при ошибке
     */
    public function create($title, $slug, $difficulty_level, $content_url, $contentJson = null) { // ИЗМЕНЕНО: content_text УДАЛЕН
        $sql = "INSERT INTO guides (title, slug, difficulty_level, content_url, content_json, created_at) VALUES (?, ?, ?, ?, ?, NOW())"; // ИЗМЕНЕНО: content_text УДАЛЕН из SQL
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$title, $slug, $difficulty_level, $content_url, $contentJson])) { // ИЗМЕНЕНО: content_text УДАЛЕН из execute
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Обновляет существующий гайд.
     * @param int $id
     * @param string $title
     * @param string $slug
     * @param string $difficulty_level
     * @param string $content_url
     * @param string|null $contentJson - контент Editor.js
     * @return bool
     */
    public function update($id, $title, $slug, $difficulty_level, $content_url, $contentJson = null) { // ИЗМЕНЕНО: content_text УДАЛЕН
        $sql = "UPDATE guides SET title = ?, slug = ?, difficulty_level = ?, content_url = ?, content_json = ? WHERE id = ?"; // ИЗМЕНЕНО: content_text УДАЛЕН из SQL
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $slug, $difficulty_level, $content_url, $contentJson, $id]); // ИЗМЕНЕНО: content_text УДАЛЕН из execute
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM guides WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getLatest($limit = 5) {
        $sql = "SELECT g.*, GROUP_CONCAT(cat.name) as categories
                FROM guides g
                LEFT JOIN guide_categories gc ON g.id = gc.guide_id
                LEFT JOIN categories cat ON gc.category_id = cat.id
                GROUP BY g.id
                ORDER BY g.created_at DESC
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryIdsForGuide($guideId) {
        $stmt = $this->pdo->prepare("SELECT category_id FROM guide_categories WHERE guide_id = ?");
        $stmt->execute([$guideId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function syncCategories($guideId, $categoryIds) {
        $stmtDelete = $this->pdo->prepare("DELETE FROM guide_categories WHERE guide_id = ?");
        $stmtDelete->execute([$guideId]);

        if (!empty($categoryIds)) {
            $stmtInsert = $this->pdo->prepare("INSERT INTO guide_categories (guide_id, category_id) VALUES (?, ?)");
            foreach ($categoryIds as $categoryId) {
                $stmtInsert->execute([$guideId, $categoryId]);
            }
        }
    }
}