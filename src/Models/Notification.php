<?php
// src/Models/Notification.php

class Notification {
    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    /**
     * Создает новое уведомление
     * @param int $userId ID пользователя
     * @param string $type Тип уведомления
     * @param string $title Заголовок
     * @param string $message Сообщение
     * @param array $data Дополнительные данные
     * @return int|false ID созданного уведомления или false
     */
    public function create($userId, $type, $title, $message, $data = []) {
        file_put_contents(__DIR__ . '/../../debug.log', "[Notification::create] userId=$userId, type=$type, title=$title, message=$message, data=" . json_encode($data) . "\n", FILE_APPEND);
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO notifications (user_id, type, title, message, data) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $success = $stmt->execute([
                $userId,
                $type,
                $title,
                $message,
                json_encode($data)
            ]);
            if ($success) {
                $notificationId = $this->pdo->lastInsertId();
                file_put_contents(__DIR__ . '/../../debug.log', "[Notification::create] SUCCESS, id=$notificationId\n", FILE_APPEND);
                $this->sendRealTime($userId, [
                    'id' => $notificationId,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                return $notificationId;
            }
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::create] FAIL\n", FILE_APPEND);
            return false;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::create] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    /**
     * Получает уведомления пользователя
     * @param int $userId ID пользователя
     * @param int $limit Лимит
     * @param int $offset Смещение
     * @return array
     */
    public function getUserNotifications($userId, $limit = 20, $offset = 0) {
        file_put_contents(__DIR__ . '/../../debug.log', "[Notification::getUserNotifications] userId=$userId, limit=$limit, offset=$offset\n", FILE_APPEND);
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $limit, $offset]);
            $result = $stmt->fetchAll();
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::getUserNotifications] result count: " . count($result) . "\n", FILE_APPEND);
            return $result;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::getUserNotifications] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    /**
     * Получает непрочитанные уведомления пользователя
     * @param int $userId ID пользователя
     * @return array
     */
    public function getUnreadNotifications($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? AND is_read = FALSE 
            ORDER BY created_at DESC
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Получает количество непрочитанных уведомлений
     * @param int $userId ID пользователя
     * @return int
     */
    public function getUnreadCount($userId) {
        file_put_contents(__DIR__ . '/../../debug.log', "[Notification::getUnreadCount] userId=$userId\n", FILE_APPEND);
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE user_id = ? AND is_read = FALSE
            ");
            
            $stmt->execute([$userId]);
            $count = (int) $stmt->fetchColumn();
            
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::getUnreadCount] count: $count\n", FILE_APPEND);
            return $count;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::getUnreadCount] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    /**
     * Отмечает уведомление как прочитанное
     * @param int $notificationId ID уведомления
     * @param int $userId ID пользователя (для безопасности)
     * @return bool
     */
    public function markAsRead($notificationId, $userId) {
        file_put_contents(__DIR__ . '/../../debug.log', "[Notification::markAsRead] notificationId=$notificationId, userId=$userId\n", FILE_APPEND);
        try {
            $stmt = $this->pdo->prepare("
                UPDATE notifications 
                SET is_read = TRUE 
                WHERE id = ? AND user_id = ?
            ");
            $result = $stmt->execute([$notificationId, $userId]);
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::markAsRead] result: " . json_encode($result) . "\n", FILE_APPEND);
            return $result;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::markAsRead] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    /**
     * Отмечает все уведомления пользователя как прочитанные
     * @param int $userId ID пользователя
     * @return bool
     */
    public function markAllAsRead($userId) {
        file_put_contents(__DIR__ . '/../../debug.log', "[Notification::markAllAsRead] userId=$userId\n", FILE_APPEND);
        try {
            $stmt = $this->pdo->prepare("
                UPDATE notifications 
                SET is_read = TRUE 
                WHERE user_id = ?
            ");
            $result = $stmt->execute([$userId]);
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::markAllAsRead] result: " . json_encode($result) . "\n", FILE_APPEND);
            return $result;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::markAllAsRead] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    /**
     * Удаляет уведомление
     * @param int $notificationId ID уведомления
     * @param int $userId ID пользователя (для безопасности)
     * @return bool
     */
    public function delete($notificationId, $userId) {
        file_put_contents(__DIR__ . '/../../debug.log', "[Notification::delete] notificationId=$notificationId, userId=$userId\n", FILE_APPEND);
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM notifications 
                WHERE id = ? AND user_id = ?
            ");
            $result = $stmt->execute([$notificationId, $userId]);
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::delete] result: " . json_encode($result) . "\n", FILE_APPEND);
            return $result;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::delete] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    /**
     * Получает уведомление по ID
     * @param int $notificationId ID уведомления
     * @param int $userId ID пользователя (для безопасности)
     * @return array|false
     */
    public function findById($notificationId, $userId) {
        file_put_contents(__DIR__ . '/../../debug.log', "[Notification::findById] notificationId=$notificationId, userId=$userId\n", FILE_APPEND);
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM notifications 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$notificationId, $userId]);
            $result = $stmt->fetch();
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::findById] result: " . json_encode($result) . "\n", FILE_APPEND);
            return $result;
        } catch (Exception $e) {
            file_put_contents(__DIR__ . '/../../debug.log', "[Notification::findById] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }

    /**
     * Отправляет уведомление через WebSocket
     * @param int $userId ID пользователя
     * @param array $notification Данные уведомления
     */
    private function sendRealTime($userId, $notification) {
        $payload = [
            'type' => 'notification',
            'userId' => $userId,
            'notification' => $notification
        ];
        $msg = json_encode($payload);
        $address = '127.0.0.1';
        $port = 8081; // Новый порт push-сервера
        try {
            $sock = fsockopen($address, $port, $errno, $errstr, 1);
            if ($sock) {
                fwrite($sock, $msg . "\n");
                fclose($sock);
            } else {
                error_log("WebSocket push error: $errstr ($errno)");
            }
        } catch (Exception $e) {
            error_log("WebSocket push exception: " . $e->getMessage());
        }
    }

    /**
     * Создает уведомление о новом сообщении
     * @param int $userId ID получателя
     * @param int $senderId ID отправителя
     * @param string $message Текст сообщения
     * @return int|false
     */
    public function createMessageNotification($userId, $senderId, $message) {
        // Получаем данные отправителя
        $userModel = new User();
        $sender = $userModel->findById($senderId);
        
        $senderName = $sender ? 
            trim(($sender['first_name'] ?? '') . ' ' . ($sender['last_name'] ?? '')) : 
            'Пользователь';
        
        return $this->create(
            $userId,
            'new_message',
            'Новое сообщение',
            "{$senderName} отправил вам сообщение",
            [
                'sender_id' => $senderId,
                'sender_name' => $senderName,
                'message_preview' => mb_substr($message, 0, 100) . (mb_strlen($message) > 100 ? '...' : '')
            ]
        );
    }

    /**
     * Создает уведомление о проверке домашнего задания
     * @param int $userId ID пользователя
     * @param string $lessonTitle Название урока
     * @param string $status Статус проверки
     * @return int|false
     */
    public function createHomeworkNotification($userId, $lessonTitle, $status) {
        $statusText = $status === 'approved' ? 'одобрено' : 'отклонено';
        
        return $this->create(
            $userId,
            'homework_checked',
            'Домашнее задание проверено',
            "Ваше домашнее задание по уроку «{$lessonTitle}» {$statusText}",
            [
                'lesson_title' => $lessonTitle,
                'status' => $status
            ]
        );
    }

    /**
     * Создает системное уведомление
     * @param int $userId ID пользователя
     * @param string $title Заголовок
     * @param string $message Сообщение
     * @param array $data Дополнительные данные
     * @return int|false
     */
    public function createSystemNotification($userId, $title, $message, $data = []) {
        return $this->create($userId, 'system', $title, $message, $data);
    }
}
