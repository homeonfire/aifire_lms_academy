<?php
// src/Models/Lesson.php

class Lesson {

    private $pdo;

    public function __construct() {
        require_once __DIR__ . '/../Config/database.php';
        $this->pdo = getDBConnection();
    }

    /**
     * Создает новый урок в модуле
     * @param int $moduleId
     * @param string $title
     * @param string $contentType
     * @return bool
     */
    /**
     * Создает новый урок в модуле
     * @param int $moduleId
     * @param string $title
     * @return bool
     */
    public function create($moduleId, $title) {
        // Определяем следующий порядковый номер урока в модуле
        $stmtOrder = $this->pdo->prepare("SELECT MAX(order_number) as max_order FROM lessons WHERE module_id = ?");
        $stmtOrder->execute([$moduleId]);
        $maxOrder = $stmtOrder->fetchColumn();
        $newOrder = ($maxOrder === null) ? 1 : $maxOrder + 1;

        // ИСПРАВЛЕНО: Убран столбец 'content_type' из запроса
        $stmt = $this->pdo->prepare(
            "INSERT INTO lessons (module_id, title, order_number) VALUES (:module_id, :title, :order_number)"
        );

        return $stmt->execute([
            'module_id' => $moduleId,
            'title' => $title,
            'order_number' => $newOrder
        ]);
    }

    /**
     * Обновляет урок
     * @param int $id
     * @param string $title
     * @param string $contentType
     * @return bool
     */
    public function update($id, $title) {
        $stmt = $this->pdo->prepare("UPDATE lessons SET title = :title WHERE id = :id");
        return $stmt->execute(['title' => $title, 'id' => $id]);
    }

    /**
     * Удаляет урок
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM lessons WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Находит один урок по его ID
     * @param int $id
     * @return mixed
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM lessons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Обновляет контент урока
     * @param int $id
     * @param string|null $contentUrl
     * @param string|null $contentText
     * @return bool
     */
    public function updateContent($id, $contentUrl, $contentText) {
        $stmt = $this->pdo->prepare(
            "UPDATE lessons SET content_url = :content_url, content_text = :content_text WHERE id = :id"
        );
        return $stmt->execute([
            'content_url' => $contentUrl,
            'content_text' => $contentText,
            'id' => $id
        ]);
    }
}