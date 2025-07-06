<?php
// websocket-server.php - Файл для запуска WebSocket сервера

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/WebSocket/NotificationServer.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// Создаем экземпляр нашего сервера
$notificationServer = new NotificationServer();

// Создаем HTTP сервер с WebSocket поддержкой
$server = IoServer::factory(
    new HttpServer(
        new WsServer($notificationServer)
    ),
    8080 // Порт для WebSocket соединений
);

echo "WebSocket сервер запущен на порту 8080\n";
echo "Для остановки нажмите Ctrl+C\n";

// Запускаем сервер
$server->run(); 