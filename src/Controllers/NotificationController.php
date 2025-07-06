<?php
// src/Controllers/NotificationController.php

require_once __DIR__ . '/../Models/Notification.php';

class NotificationController {
    private $notificationModel;

    public function __construct() {
        // Инициализируем модель уведомлений
        $this->notificationModel = new Notification();
    }

    public function render($view, $data = []) {
        try {
            // Превращаем ключи массива в переменные (например, $data['title'] станет $title)
            extract($data);

            // Формируем путь к файлу вида
            $viewPath = __DIR__ . '/../Views/' . $view . '.php';

            error_log("NotificationController::render - View path: $viewPath");
            error_log("NotificationController::render - View exists: " . (file_exists($viewPath) ? 'YES' : 'NO'));

            if (file_exists($viewPath)) {
                require $viewPath;
            } else {
                // Если файл не найден, выводим ошибку
                error_log("NotificationController::render - View file not found: $viewPath");
                die("Ошибка: файл вида не найден по пути: " . $viewPath);
            }
        } catch (Exception $e) {
            error_log("NotificationController::render - Error: " . $e->getMessage());
            error_log("NotificationController::render - Stack trace: " . $e->getTraceAsString());
            echo "Ошибка рендеринга: " . $e->getMessage();
        }
    }

    private function getNotificationModel() {
        if (!$this->notificationModel) {
            $this->notificationModel = new Notification();
        }
        return $this->notificationModel;
    }

    /**
     * Показывает список уведомлений пользователя
     */
    public function index() {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::index] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);
        try {
            if (!isset($_SESSION['user'])) {
                header('Location: /login');
                exit();
            }
            $userId = $_SESSION['user']['id'];
            $page = (int)($_GET['page'] ?? 1);
            $limit = 20;
            $offset = ($page - 1) * $limit;
            $notifications = $this->getNotificationModel()->getUserNotifications($userId, $limit, $offset);
            $unreadCount = $this->getNotificationModel()->getUnreadCount($userId);
            file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::index] notifications: " . json_encode($notifications) . ", unreadCount: $unreadCount\n", FILE_APPEND);
            $this->render('notifications/index', [
                'title' => 'Уведомления',
                'notifications' => $notifications,
                'unreadCount' => $unreadCount,
                'currentPage' => $page
            ]);
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::index] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            echo "Ошибка: " . $e->getMessage();
        }
    }

    /**
     * Получает уведомления в формате JSON (для AJAX)
     */
    public function getNotifications() {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::getNotifications] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $page = (int)($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $notifications = $this->getNotificationModel()->getUserNotifications($userId, $limit, $offset);
        $unreadCount = $this->getNotificationModel()->getUnreadCount($userId);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::getNotifications] notifications: " . json_encode($notifications) . ", unreadCount: $unreadCount\n", FILE_APPEND);
        foreach ($notifications as &$notification) {
            $notification['data'] = json_decode($notification['data'], true);
            $notification['created_at_formatted'] = date('d.m.Y H:i', strtotime($notification['created_at']));
        }
        header('Content-Type: application/json');
        echo json_encode([
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'hasMore' => count($notifications) === $limit
        ]);
    }

    /**
     * Получает количество непрочитанных уведомлений
     */
    public function getUnreadCount() {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::getUnreadCount] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $unreadCount = $this->getNotificationModel()->getUnreadCount($userId);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::getUnreadCount] unreadCount: $unreadCount\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode(['unreadCount' => $unreadCount]);
    }

    /**
     * Отмечает уведомление как прочитанное
     */
    public function markAsRead() {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::markAsRead] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $input = json_decode(file_get_contents('php://input'), true);
        $notificationId = (int)($input['notification_id'] ?? 0);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::markAsRead] notificationId: $notificationId\n", FILE_APPEND);
        if (!$notificationId) {
            http_response_code(400);
            echo json_encode(['error' => 'Notification ID required']);
            exit();
        }
        $success = $this->getNotificationModel()->markAsRead($notificationId, $userId);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::markAsRead] success: " . json_encode($success) . "\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Отмечает все уведомления как прочитанные
     */
    public function markAllAsRead() {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::markAllAsRead] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $success = $this->getNotificationModel()->markAllAsRead($userId);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::markAllAsRead] success: " . json_encode($success) . "\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Удаляет уведомление
     */
    public function delete() {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::delete] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $input = json_decode(file_get_contents('php://input'), true);
        $notificationId = (int)($input['notification_id'] ?? 0);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::delete] notificationId: $notificationId\n", FILE_APPEND);
        if (!$notificationId) {
            http_response_code(400);
            echo json_encode(['error' => 'Notification ID required']);
            exit();
        }
        $success = $this->getNotificationModel()->delete($notificationId, $userId);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::delete] success: " . json_encode($success) . "\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Показывает уведомление в модальном окне
     */
    public function show($id) {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::show] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . ", id: $id\n", FILE_APPEND);
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $notification = $this->getNotificationModel()->findById($id, $userId);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::show] notification: " . json_encode($notification) . "\n", FILE_APPEND);
        if (!$notification) {
            http_response_code(404);
            echo json_encode(['error' => 'Notification not found']);
            exit();
        }
        $this->getNotificationModel()->markAsRead($id, $userId);
        $notification['data'] = json_decode($notification['data'], true);
        $notification['created_at_formatted'] = date('d.m.Y H:i', strtotime($notification['created_at']));
        header('Content-Type: application/json');
        echo json_encode($notification);
    }

    /**
     * Создает тестовое уведомление (для отладки)
     */
    public function createTest() {
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::createTest] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);
        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }
        $userId = $_SESSION['user']['id'];
        $type = $_POST['type'] ?? 'system';
        $title = $_POST['title'] ?? 'Тестовое уведомление';
        $message = $_POST['message'] ?? 'Это тестовое уведомление';
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::createTest] type: $type, title: $title, message: $message\n", FILE_APPEND);
        $notificationId = $this->getNotificationModel()->create($userId, $type, $title, $message);
        file_put_contents(__DIR__ . '/../../debug.log', "[NotificationController::createTest] notificationId: $notificationId\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $notificationId !== false,
            'notificationId' => $notificationId
        ]);
    }

    /**
     * Создает уведомление по GET-запросу (для теста через ссылку)
     * URL: /notifications/create-link-test
     */
    public function createLinkTest() {
        file_put_contents('debug.log', "[NotificationController::createLinkTest] called, user_id: " . ($_SESSION['user']['id'] ?? 'null') . "\n", FILE_APPEND);

        if (!isset($_SESSION['user'])) {
            echo "Вы не авторизованы!";
            return;
        }

        $userId = $_SESSION['user']['id'];
        $type = 'system';
        $title = 'Тестовое уведомление по ссылке';
        $message = 'Это уведомление создано через GET-запрос по ссылке!';

        $notificationId = $this->getNotificationModel()->create($userId, $type, $title, $message);

        if ($notificationId) {
            echo "Уведомление успешно создано! ID: $notificationId";
        } else {
            echo "Ошибка создания уведомления!";
        }
    }
}
