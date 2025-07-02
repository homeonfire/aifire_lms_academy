<?php
// src/Models/User.php

class User {

    private $pdo;

    public function __construct() {
        // Подключаем функцию и получаем соединение
        require_once __DIR__ . '/../Config/database.php';
        $this->pdo = getDBConnection();
    }

    /**
     * Поиск пользователя по email
     * @param string $email
     * @return mixed (массив с данными пользователя или false)
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Создание нового пользователя
     * @param string $email
     * @param string $passwordHash
     * @return bool
     */
    public function create($email, $passwordHash) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (email, password_hash) VALUES (?, ?)"
        );
        return $stmt->execute([$email, $passwordHash]);
    }

    /**
     * Находит одного пользователя по его ID
     * @param int $id
     * @return mixed
     */
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Обновляет данные пользователя
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET 
            first_name = :first_name, 
            last_name = :last_name, 
            experience_level = :experience_level, 
            preferred_skill_type = :preferred_skill_type 
         WHERE id = :id"
        );

        // Привязываем значения
        $stmt->bindValue(':first_name', $data['first_name']);
        $stmt->bindValue(':last_name', $data['last_name']);
        $stmt->bindValue(':experience_level', $data['experience_level']);
        $stmt->bindValue(':preferred_skill_type', $data['preferred_skill_type']);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}