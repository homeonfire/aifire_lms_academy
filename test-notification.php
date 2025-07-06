<?php
// test-notification.php - Тестовый файл для проверки уведомлений

file_put_contents(__DIR__ . '/debug.log', "[test-notification] START\n", FILE_APPEND);

file_put_contents(__DIR__ . '/debug.log', "[test-notification] after require database.php\n", FILE_APPEND);
require_once __DIR__ . '/src/Config/database.php';

file_put_contents(__DIR__ . '/debug.log', "[test-notification] after require Notification.php\n", FILE_APPEND);
require_once __DIR__ . '/src/Models/Notification.php';

file_put_contents(__DIR__ . '/debug.log', "[test-notification] before new Notification\n", FILE_APPEND);
$notificationModel = new Notification();
file_put_contents(__DIR__ . '/debug.log', "[test-notification] after new Notification\n", FILE_APPEND);


// ID пользователя для тестирования (замените на реальный ID)
$testUserId = 1;
file_put_contents(__DIR__ . '/debug.log', "[test-notification] testUserId: $testUserId\n", FILE_APPEND);

echo "Создаем тестовое уведомление для пользователя ID: {$testUserId}\n";

try {
    $notificationId = $notificationModel->create(
        $testUserId,
        'test',
        'Тестовое уведомление',
        'Это тестовое уведомление для проверки системы',
        ['test_data' => 'test_value']
    );
    file_put_contents(__DIR__ . '/debug.log', "[test-notification] create() result: $notificationId\n", FILE_APPEND);

    if ($notificationId) {
        echo "Уведомление создано успешно! ID: {$notificationId}\n";
        // Проверяем количество непрочитанных
        $unreadCount = $notificationModel->getUnreadCount($testUserId);
        echo "Количество непрочитанных уведомлений: {$unreadCount}\n";
        file_put_contents(__DIR__ . '/debug.log', "[test-notification] getUnreadCount: $unreadCount\n", FILE_APPEND);
        // Получаем все уведомления пользователя
        $notifications = $notificationModel->getUserNotifications($testUserId, 10);
        echo "Всего уведомлений у пользователя: " . count($notifications) . "\n";
        file_put_contents(__DIR__ . '/debug.log', "[test-notification] getUserNotifications count: " . count($notifications) . "\n", FILE_APPEND);
    } else {
        echo "Ошибка создания уведомления\n";
        file_put_contents(__DIR__ . '/debug.log', "[test-notification] ERROR: create() вернул false\n", FILE_APPEND);
    }
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    file_put_contents(__DIR__ . '/debug.log', "[test-notification] EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
}
file_put_contents(__DIR__ . '/debug.log', "[test-notification] END\n", FILE_APPEND); 