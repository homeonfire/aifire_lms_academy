<?php
// test_subscription.php
// Тестовый скрипт для проверки системы подписок

require_once 'vendor/autoload.php';
require_once 'src/Congif/database.php';

try {
    $pdo = getDBConnection();
    
    echo "=== Тест системы подписок ===\n\n";
    
    // 1. Проверяем, есть ли курсы в базе
    $stmt = $pdo->query("SELECT id, title, price, is_free FROM courses LIMIT 5");
    $courses = $stmt->fetchAll();
    
    echo "Курсы в базе данных:\n";
    foreach ($courses as $course) {
        echo "- ID: {$course['id']}, Название: {$course['title']}, Цена: {$course['price']}, Бесплатный: " . ($course['is_free'] ? 'Да' : 'Нет') . "\n";
    }
    echo "\n";
    
    // 2. Проверяем, есть ли пользователи
    $stmt = $pdo->query("SELECT id, email FROM users LIMIT 3");
    $users = $stmt->fetchAll();
    
    echo "Пользователи в базе данных:\n";
    foreach ($users as $user) {
        echo "- ID: {$user['id']}, Email: {$user['email']}\n";
    }
    echo "\n";
    
    // 3. Проверяем структуру таблицы subscriptions
    $stmt = $pdo->query("DESCRIBE subscriptions");
    $columns = $stmt->fetchAll();
    
    echo "Структура таблицы subscriptions:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']}\n";
    }
    echo "\n";
    
    // 4. Тестируем модель Subscription
    if (!empty($courses) && !empty($users)) {
        $subscriptionModel = new Subscription();
        $courseId = $courses[0]['id'];
        $userId = $users[0]['id'];
        
        echo "Тестируем модель Subscription:\n";
        echo "- Курс ID: {$courseId}\n";
        echo "- Пользователь ID: {$userId}\n";
        
        // Проверяем подписку
        $hasSubscription = $subscriptionModel->hasActiveSubscription($userId, $courseId);
        echo "- Есть подписка на курс: " . ($hasSubscription ? 'Да' : 'Нет') . "\n";
        
        // Проверяем общую подписку
        $hasGeneral = $subscriptionModel->hasGeneralSubscription($userId);
        echo "- Есть общая подписка: " . ($hasGeneral ? 'Да' : 'Нет') . "\n";
        
        // Тестируем модель Course
        $courseModel = new Course();
        $hasAccess = $courseModel->hasAccess($userId, $courseId);
        echo "- Есть доступ к курсу: " . ($hasAccess ? 'Да' : 'Нет') . "\n";
    }
    
    echo "\n=== Тест завершен ===\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} 