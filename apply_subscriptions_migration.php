<?php
// apply_subscriptions_migration.php
// Скрипт для применения миграции subscriptions

require_once 'vendor/autoload.php';
require_once 'src/Congif/database.php';

try {
    $pdo = getDBConnection();
    
    echo "Применяем миграцию subscriptions...\n";
    
    // Читаем SQL файл
    $sql = file_get_contents('database/subscriptions_update.sql');
    
    // Выполняем SQL
    $pdo->exec($sql);
    
    echo "✅ Миграция успешно применена!\n";
    echo "Добавлено поле course_id_access в таблицу subscriptions\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка при применении миграции: " . $e->getMessage() . "\n";
} 