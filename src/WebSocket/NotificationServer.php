<?php
// src/WebSocket/NotificationServer.php
require __DIR__ . '/../../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server as ReactServer;

class NotificationServer implements MessageComponentInterface {
    protected $clients;
    protected $userConnections = [];
    protected $userTokens = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Новое соединение! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!$data) {
            return;
        }
        switch ($data['type']) {
            case 'auth':
                $this->handleAuth($from, $data);
                break;
            case 'ping':
                $this->handlePing($from);
                break;
            default:
                echo "Неизвестный тип сообщения: {$data['type']}\n";
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        foreach ($this->userConnections as $userId => $connection) {
            if ($connection === $conn) {
                unset($this->userConnections[$userId]);
                unset($this->userTokens[$userId]);
                break;
            }
        }
        echo "Соединение {$conn->resourceId} закрыто\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Ошибка: {$e->getMessage()}\n";
        $conn->close();
    }

    private function handleAuth(ConnectionInterface $from, $data) {
        $token = $data['token'] ?? null;
        $userId = $data['userId'] ?? null;
        if (!$token || !$userId) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Неверные данные аутентификации'
            ]));
            return;
        }
        $this->userConnections[$userId] = $from;
        $this->userTokens[$userId] = $token;
        echo "Пользователь {$userId} аутентифицирован\n";
        $from->send(json_encode([
            'type' => 'auth_success',
            'message' => 'Аутентификация успешна'
        ]));
    }

    private function handlePing(ConnectionInterface $from) {
        $from->send(json_encode([
            'type' => 'pong',
            'timestamp' => time()
        ]));
    }

    public function sendNotification($userId, $notification) {
        if (isset($this->userConnections[$userId])) {
            $this->userConnections[$userId]->send(json_encode([
                'type' => 'notification',
                'data' => $notification
            ]));
            return true;
        }
        return false;
    }

    public function broadcastNotification($notification) {
        foreach ($this->userConnections as $connection) {
            $connection->send(json_encode([
                'type' => 'notification',
                'data' => $notification
            ]));
        }
    }

    public function getActiveConnectionsCount() {
        return count($this->userConnections);
    }

    public function getActiveUsers() {
        return array_keys($this->userConnections);
    }
}

// --- REACTPHP EVENT LOOP ---
$loop = Factory::create();

// WebSocket сервер для браузеров (порт 8080)
$notificationServer = new NotificationServer();
$webSock = new ReactServer('0.0.0.0:8080', $loop);
$webServer = new IoServer(
    new HttpServer(
        new WsServer($notificationServer)
    ),
    $webSock,
    $loop
);

echo "WebSocket сервер запущен на порту 8080\n";

// Push-listener для PHP (порт 8081)
$pushSock = new ReactServer('127.0.0.1:8081', $loop);
$pushSock->on('connection', function ($conn) use ($notificationServer) {
    $conn->on('data', function ($data) use ($notificationServer) {
        $msg = trim($data);
        $data = json_decode($msg, true);
        if ($data && $data['type'] === 'notification') {
            $userId = $data['userId'];
            $notification = $data['notification'];
            $notificationServer->sendNotification($userId, $notification);
            echo "Push-уведомление отправлено userId=$userId\n";
        }
    });
});
echo "Push-listener запущен на порту 8081\n";

$loop->run();
