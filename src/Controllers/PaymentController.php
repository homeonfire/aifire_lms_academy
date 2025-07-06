<?php
// src/Controllers/PaymentController.php

class PaymentController extends Controller {
    
    public function __construct() {
        // Проверяем авторизацию для всех методов кроме notification
        if ($this->getCurrentAction() !== 'notification') {
            $this->requireAuth();
        }
    }

    /**
     * Показывает страницу покупки курса
     */
    public function buyCourse() {
        $courseId = $_GET['course_id'] ?? null;
        
        if (!$courseId) {
            $this->redirect('/courses');
        }

        $courseModel = new Course();
        $course = $courseModel->findById($courseId);
        
        if (!$course) {
            $this->redirect('/courses');
        }

        // Проверяем, не купил ли уже пользователь этот курс
        $paymentModel = new Payment();
        if ($paymentModel->isCoursePaid($_SESSION['user']['id'], $courseId)) {
            $this->redirect('/courses/show?id=' . $courseId);
        }

        $this->render('payment/buy-course', [
            'course' => $course,
            'title' => 'Покупка курса: ' . $course['title']
        ]);
    }

    /**
     * Создает платеж и перенаправляет на Т-Банк
     */
    public function createPayment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/courses');
        }

        $courseId = $_POST['course_id'] ?? null;
        $userId = $_SESSION['user']['id'];

        if (!$courseId) {
            $this->redirect('/courses');
        }

        $courseModel = new Course();
        $course = $courseModel->findById($courseId);
        
        if (!$course) {
            $this->redirect('/courses');
        }

        // Проверяем, не купил ли уже пользователь этот курс
        $paymentModel = new Payment();
        if ($paymentModel->isCoursePaid($userId, $courseId)) {
            $this->redirect('/courses/show?id=' . $courseId);
        }

        // Создаем платеж в нашей системе
        $paymentId = $paymentModel->create($userId, $courseId, $course['price']);

        if (!$paymentId) {
            $_SESSION['error'] = 'Ошибка создания платежа';
            $this->redirect('/payment/buy-course?course_id=' . $courseId);
        }

        // Создаем платеж в Т-Банке
        $tbankService = new TBankPaymentService();
        $returnUrl = $this->getBaseUrl() . '/payment/success';
        
        $paymentData = $tbankService->createPayment(
            $paymentId,
            $course['price'],
            'Покупка курса: ' . $course['title'],
            $returnUrl
        );

        if (!$paymentData) {
            $_SESSION['error'] = 'Ошибка создания платежа в Т-Банке';
            $this->redirect('/payment/buy-course?course_id=' . $courseId);
        }

        // Сохраняем URL платежа в базе
        $paymentModel->updateStatus($paymentId, 'pending', $paymentData['transaction_id']);
        
        // Перенаправляем на страницу оплаты Т-Банка
        header('Location: ' . $paymentData['payment_url']);
        exit;
    }

    /**
     * Обрабатывает успешную оплату
     */
    public function success() {
        $paymentId = $_GET['payment_id'] ?? null;
        $status = $_GET['status'] ?? 'success';

        if ($status === 'failed') {
            $_SESSION['error'] = 'Оплата не была завершена';
            $this->redirect('/courses');
        }

        if ($paymentId) {
            $paymentModel = new Payment();
            $payment = $paymentModel->findById($paymentId);
            
            if ($payment && $payment['user_id'] == $_SESSION['user']['id']) {
                $this->render('payment/success', [
                    'payment' => $payment,
                    'title' => 'Оплата прошла успешно'
                ]);
                return;
            }
        }

        $this->render('payment/success', [
            'payment' => null,
            'title' => 'Оплата прошла успешно'
        ]);
    }

    /**
     * Обрабатывает уведомления от Т-Банка
     */
    public function notification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method Not Allowed');
        }

        // Получаем данные уведомления
        $notificationData = $_POST;
        
        // Логируем уведомление
        error_log('TBank notification received: ' . json_encode($notificationData));

        $tbankService = new TBankPaymentService();
        $result = $tbankService->processNotification($notificationData);

        if ($result) {
            http_response_code(200);
            echo 'OK';
        } else {
            http_response_code(400);
            echo 'ERROR';
        }
    }

    /**
     * Показывает историю платежей пользователя
     */
    public function history() {
        $paymentModel = new Payment();
        $payments = $paymentModel->getByUserId($_SESSION['user']['id']);

        $this->render('payment/history', [
            'payments' => $payments,
            'title' => 'История платежей'
        ]);
    }

    /**
     * Проверяет статус платежа (AJAX)
     */
    public function checkStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $paymentId = $_POST['payment_id'] ?? null;
        $userId = $_SESSION['user']['id'];

        if (!$paymentId) {
            http_response_code(400);
            echo json_encode(['error' => 'Payment ID required']);
            exit;
        }

        $paymentModel = new Payment();
        $payment = $paymentModel->findById($paymentId);

        if (!$payment || $payment['user_id'] != $userId) {
            http_response_code(404);
            echo json_encode(['error' => 'Payment not found']);
            exit;
        }

        // Если есть ID транзакции Т-Банка, проверяем статус
        if ($payment['tbank_transaction_id']) {
            $tbankService = new TBankPaymentService();
            $status = $tbankService->checkPaymentStatus($payment['tbank_transaction_id']);
            
            if ($status) {
                $paymentModel->updateStatus($paymentId, $status['status'], $payment['tbank_transaction_id']);
                $payment['status'] = $status['status'];
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status' => $payment['status'],
            'payment_id' => $payment['id']
        ]);
    }

    /**
     * Получает базовый URL сайта
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host;
    }
} 