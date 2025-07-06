<?php
// src/Models/Subscription.php

class Subscription {

    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    /**
     * Проверяет, есть ли у пользователя активная подписка на курс
     * @param int $userId ID пользователя
     * @param int $courseId ID курса
     * @return bool
     */
    public function hasActiveSubscription($userId, $courseId) {
        $sql = "SELECT COUNT(*) FROM subscriptions 
                WHERE user_id = ? 
                AND status = 'active' 
                AND course_id_access = ?
                AND (end_date IS NULL OR end_date > NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $courseId]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Создает подписку на курс для пользователя
     * @param int $userId ID пользователя
     * @param int $courseId ID курса
     * @param string $planType Тип плана (monthly, yearly, lifetime)
     * @param float $amount Сумма
     * @param string $currency Валюта
     * @param string $tbankSubscriptionId ID подписки в Т-Банке
     * @return int|false ID созданной подписки
     */
    public function createSubscription($userId, $courseId, $planType, $amount, $currency = 'RUB', $tbankSubscriptionId = null) {
        $sql = "INSERT INTO subscriptions (user_id, course_id_access, plan_type, amount, currency, tbank_subscription_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$userId, $courseId, $planType, $amount, $currency, $tbankSubscriptionId]);
        
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        
        return false;
    }

    /**
     * Получает все активные подписки пользователя
     * @param int $userId ID пользователя
     * @return array
     */
    public function getUserActiveSubscriptions($userId) {
        $sql = "SELECT s.*, c.title as course_title 
                FROM subscriptions s 
                LEFT JOIN courses c ON s.course_id_access = c.id 
                WHERE s.user_id = ? 
                AND s.status = 'active' 
                AND (s.end_date IS NULL OR s.end_date > NOW())
                ORDER BY s.created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll();
    }

    /**
     * Отменяет подписку
     * @param int $subscriptionId ID подписки
     * @return bool
     */
    public function cancelSubscription($subscriptionId) {
        $sql = "UPDATE subscriptions SET status = 'cancelled' WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([$subscriptionId]);
    }

    /**
     * Проверяет, есть ли у пользователя общая подписка (без привязки к курсу)
     * @param int $userId ID пользователя
     * @return bool
     */
    public function hasGeneralSubscription($userId) {
        $sql = "SELECT COUNT(*) FROM subscriptions 
                WHERE user_id = ? 
                AND status = 'active' 
                AND course_id_access IS NULL
                AND (end_date IS NULL OR end_date > NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Получает подписку по ID
     * @param int $subscriptionId ID подписки
     * @return array|false
     */
    public function findById($subscriptionId) {
        $sql = "SELECT s.*, c.title as course_title 
                FROM subscriptions s 
                LEFT JOIN courses c ON s.course_id_access = c.id 
                WHERE s.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$subscriptionId]);
        
        return $stmt->fetch();
    }
} 