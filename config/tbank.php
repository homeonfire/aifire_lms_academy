<?php
// config/tbank.php

return [
    // Конфигурация Т-Банка
    'merchant_id' => $_ENV['TBANK_MERCHANT_ID'] ?? 'test_merchant',
    'secret_key' => $_ENV['TBANK_SECRET_KEY'] ?? 'test_secret',
    
    // Режим работы (true - тестовый, false - продакшен)
    'test_mode' => $_ENV['TBANK_TEST_MODE'] ?? true,
    
    // URL для уведомлений
    'notification_url' => $_ENV['TBANK_NOTIFICATION_URL'] ?? null,
    
    // API URLs
    'api_urls' => [
        'test' => 'https://test-payment.tbank.ru/api/v1',
        'production' => 'https://payment.tbank.ru/api/v1'
    ],
    
    // Настройки платежей
    'payment' => [
        'currency' => 'RUB',
        'timeout' => 30, // секунды
        'retry_attempts' => 3
    ]
]; 