<?php
// src/Services/TBankPaymentService.php

class TBankPaymentService {
    private $apiUrl;
    private $merchantId;
    private $secretKey;
    private $isTestMode;

    public function __construct() {
        // Конфигурация Т-Банка
        $this->isTestMode = true; // Переключить на false для продакшена
        $this->apiUrl = $this->isTestMode 
            ? 'https://test-payment.tbank.ru/api/v1' 
            : 'https://payment.tbank.ru/api/v1';
        $this->merchantId = $_ENV['TBANK_MERCHANT_ID'] ?? 'test_merchant';
        $this->secretKey = $_ENV['TBANK_SECRET_KEY'] ?? 'test_secret';
    }

    /**
     * Создает платеж в Т-Банке
     * @param int $paymentId ID платежа в нашей системе
     * @param float $amount Сумма платежа
     * @param string $description Описание платежа
     * @param string $returnUrl URL для возврата после оплаты
     * @return array|false
     */
    public function createPayment($paymentId, $amount, $description, $returnUrl) {
        $data = [
            'merchant_id' => $this->merchantId,
            'order_id' => $paymentId,
            'amount' => $amount * 100, // Т-Банк принимает копейки
            'currency' => 'RUB',
            'description' => $description,
            'return_url' => $returnUrl,
            'fail_url' => $returnUrl . '?status=failed',
            'notification_url' => $this->getNotificationUrl(),
            'timestamp' => time()
        ];

        $data['signature'] = $this->generateSignature($data);

        $response = $this->makeRequest('POST', '/payments/create', $data);
        
        if ($response && isset($response['success']) && $response['success']) {
            return [
                'payment_url' => $response['data']['payment_url'],
                'transaction_id' => $response['data']['transaction_id']
            ];
        }

        return false;
    }

    /**
     * Проверяет статус платежа
     * @param string $transactionId ID транзакции в Т-Банке
     * @return array|false
     */
    public function checkPaymentStatus($transactionId) {
        $data = [
            'merchant_id' => $this->merchantId,
            'transaction_id' => $transactionId,
            'timestamp' => time()
        ];

        $data['signature'] = $this->generateSignature($data);

        $response = $this->makeRequest('POST', '/payments/status', $data);
        
        if ($response && isset($response['success']) && $response['success']) {
            return $response['data'];
        }

        return false;
    }

    /**
     * Обрабатывает уведомление от Т-Банка
     * @param array $notificationData Данные уведомления
     * @return bool
     */
    public function processNotification($notificationData) {
        // Проверяем подпись уведомления
        if (!$this->verifyNotificationSignature($notificationData)) {
            error_log('TBank: Invalid notification signature');
            return false;
        }

        $paymentId = $notificationData['order_id'];
        $transactionId = $notificationData['transaction_id'];
        $status = $notificationData['status'];

        // Обновляем статус платежа в нашей системе
        $paymentModel = new Payment();
        $paymentModel->updateStatus($paymentId, $status, $transactionId);

        return true;
    }

    /**
     * Генерирует подпись для запроса
     * @param array $data Данные для подписи
     * @return string
     */
    private function generateSignature($data) {
        // Убираем signature из данных перед генерацией подписи
        unset($data['signature']);
        
        // Сортируем ключи
        ksort($data);
        
        // Создаем строку для подписи
        $signString = '';
        foreach ($data as $key => $value) {
            $signString .= $key . '=' . $value . '&';
        }
        $signString = rtrim($signString, '&');
        
        // Добавляем секретный ключ
        $signString .= $this->secretKey;
        
        // Генерируем MD5 хеш
        return md5($signString);
    }

    /**
     * Проверяет подпись уведомления
     * @param array $notificationData
     * @return bool
     */
    private function verifyNotificationSignature($notificationData) {
        $receivedSignature = $notificationData['signature'] ?? '';
        $calculatedSignature = $this->generateSignature($notificationData);
        
        return $receivedSignature === $calculatedSignature;
    }

    /**
     * Выполняет HTTP запрос к API Т-Банка
     * @param string $method HTTP метод
     * @param string $endpoint Эндпоинт API
     * @param array $data Данные для отправки
     * @return array|false
     */
    private function makeRequest($method, $endpoint, $data) {
        $url = $this->apiUrl . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            error_log('TBank API request failed: ' . $response);
            return false;
        }

        $decodedResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('TBank API invalid JSON response: ' . $response);
            return false;
        }

        return $decodedResponse;
    }

    /**
     * Получает URL для уведомлений
     * @return string
     */
    private function getNotificationUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host . '/payment/notification';
    }

    /**
     * Возвращает информацию о тестовом режиме
     * @return bool
     */
    public function isTestMode() {
        return $this->isTestMode;
    }
} 