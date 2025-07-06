<?php
// debug_courses.php
// Отладочный скрипт для проверки курсов и доступа

require_once 'vendor/autoload.php';
require_once 'src/Congif/database.php';

session_start();

try {
    $pdo = getDBConnection();
    
    echo "=== Отладка курсов и доступа ===\n\n";
    
    // 1. Проверяем курсы
    $stmt = $pdo->query("SELECT id, title, price, is_free, type FROM courses LIMIT 5");
    $courses = $stmt->fetchAll();
    
    echo "Курсы в базе данных:\n";
    foreach ($courses as $course) {
        echo "- ID: {$course['id']}, Название: {$course['title']}, Цена: {$course['price']}, Бесплатный: " . ($course['is_free'] ? 'Да' : 'Нет') . ", Тип: {$course['type']}\n";
    }
    echo "\n";
    
    // 2. Проверяем пользователей
    $stmt = $pdo->query("SELECT id, email FROM users LIMIT 3");
    $users = $stmt->fetchAll();
    
    echo "Пользователи в базе данных:\n";
    foreach ($users as $user) {
        echo "- ID: {$user['id']}, Email: {$user['email']}\n";
    }
    echo "\n";
    
    // 3. Проверяем подписки
    $stmt = $pdo->query("SELECT id, user_id, course_id_access, status, end_date FROM subscriptions LIMIT 5");
    $subscriptions = $stmt->fetchAll();
    
    echo "Подписки в базе данных:\n";
    foreach ($subscriptions as $sub) {
        echo "- ID: {$sub['id']}, User: {$sub['user_id']}, Course: " . ($sub['course_id_access'] ?? 'NULL') . ", Status: {$sub['status']}, End: " . ($sub['end_date'] ?? 'NULL') . "\n";
    }
    echo "\n";
    
    // 4. Тестируем модель Course
    if (!empty($courses) && !empty($users)) {
        $courseModel = new Course();
        $userId = $users[0]['id'];
        
        echo "Тестируем доступ для пользователя ID {$userId}:\n";
        foreach ($courses as $course) {
            $hasAccess = $courseModel->hasAccess($userId, $course['id']);
            $isFree = !empty($course['is_free']) || $course['price'] == 0;
            
            echo "- Курс '{$course['title']}' (ID: {$course['id']}):\n";
            echo "  Бесплатный: " . ($isFree ? 'Да' : 'Нет') . "\n";
            echo "  Есть доступ: " . ($hasAccess ? 'Да' : 'Нет') . "\n";
            echo "  Должна показываться кнопка: ";
            
            if ($hasAccess) {
                echo "✓ Доступен\n";
            } elseif ($isFree) {
                echo "Начать\n";
            } else {
                echo "Купить\n";
            }
            echo "\n";
        }
    }
    
    echo "=== Отладка завершена ===\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} 