<?php
// src/Models/Guide.php

class Guide {
    private $pdo;

    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function __construct() {
        require_once __DIR__ . '/../Config/database.php';
        $this->pdo = getDBConnection();
    }
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---

    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
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
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---

    // --- БЕЗ ИЗМЕНЕНИЙ ---
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
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---

    // --- НАЧАЛО ИЗМЕНЕНИЙ ---
    /**
     * Создает новый гайд.
     */
    public function create($title, $slug, $difficulty_level, $content_text, $content_url) {
        $sql = "INSERT INTO guides (title, slug, difficulty_level, content_text, content_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$title, $slug, $difficulty_level, $content_text, $content_url])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    /**
     * Обновляет существующий гайд.
     */
    public function update($id, $title, $slug, $difficulty_level, $content_text, $content_url) {
        $sql = "UPDATE guides SET title = ?, slug = ?, difficulty_level = ?, content_text = ?, content_url = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$title, $slug, $difficulty_level, $content_text, $content_url, $id]);
    }
    // --- КОНЕЦ ИЗМЕНЕНИЙ ---

    // --- БЕЗ ИЗМЕНЕНИЙ ---
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM guides WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getLatest($limit = 5) {
        // Мы обновим этот метод, чтобы он тоже получал категории
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
    // --- КОНЕЦ БЛОКА БЕЗ ИЗМЕНЕНИЙ ---

    // --- НОВЫЕ МЕТОДЫ ---
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
    // --- КОНЕЦ НОВЫХ МЕТОДОВ ---
}