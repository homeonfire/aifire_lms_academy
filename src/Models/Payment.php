<?php
// src/Models/Payment.php

class Payment {
    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    /**
     * Создает новый платеж
     * @param int $userId ID пользователя
     * @param int $courseId ID курса
     * @param float $amount Сумма платежа
     * @param string $currency Валюта (RUB)
     * @return int|false ID созданного платежа
     */
    public function create($userId, $courseId, $amount, $currency = 'RUB') {
        $stmt = $this->pdo->prepare("
            INSERT INTO payments (user_id, course_id, amount, currency, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$userId, $courseId, $amount, $currency]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Обновляет статус платежа
     * @param int $paymentId ID платежа
     * @param string $status Новый статус
     * @param string $tbankTransactionId ID транзакции в Т-Банке
     * @return bool
     */
    public function updateStatus($paymentId, $status, $tbankTransactionId = null) {
        $stmt = $this->pdo->prepare("
            UPDATE payments 
            SET status = ?, tbank_transaction_id = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $tbankTransactionId, $paymentId]);
    }

    /**
     * Получает платеж по ID
     * @param int $paymentId
     * @return array|false
     */
    public function findById($paymentId) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.title as course_title, u.email as user_email 
            FROM payments p
            JOIN courses c ON p.course_id = c.id
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$paymentId]);
        return $stmt->fetch();
    }

    /**
     * Получает все платежи пользователя
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, c.title as course_title 
            FROM payments p
            JOIN courses c ON p.course_id = c.id
            WHERE p.user_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Проверяет, оплатил ли пользователь курс
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    public function isCoursePaid($userId, $courseId) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM payments 
            WHERE user_id = ? AND course_id = ? AND status = 'completed'
        ");
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Получает статистику платежей
     * @return array
     */
    public function getStatistics() {
        $stmt = $this->pdo->prepare("
            SELECT 
                COUNT(*) as total_payments,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_payments,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_payments,
                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_revenue
            FROM payments
        ");
        $stmt->execute();
        return $stmt->fetch();
    }
} 