<?php
require_once "app/core/Controller.php";

class AuthController extends Controller {
    public function login() {
        if (!empty($_GET['hash'])) {
            $this->handleTelegramAuth($_GET);
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEmailLogin($_POST);
        } else {
            $this->render('auth/login', [
                'telegram_bot_id' => TELEGRAM_BOT_ID
            ]);
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEmailRegistration($_POST);
        } else {
            $this->render('auth/register');
        }
    }

    public function profile() {
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            exit;
        }

        $user = User::find($_SESSION['user_id']);
        $this->render('auth/profile', [
            'user' => $user
        ]);
    }

    public function logout() {
        // Уничтожаем сессию
        session_destroy();

        // Редирект на главную
        header('Location: /');
        exit;
    }

    private function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    private function handleTelegramAuth($data, $isAttach = false) {
        // Проверка подписи Telegram
        $check_hash = $data['hash'];
        $auth_data = [];

        // Собираем только нужные поля
        $allowed_keys = ['id', 'first_name', 'last_name', 'username', 'photo_url', 'auth_date'];
        foreach ($allowed_keys as $key) {
            if (isset($data[$key])) {
                $auth_data[$key] = $data[$key];
            }
        }

        // Проверяем время авторизации (не старше 5 минут)
        if (time() - $auth_data['auth_date'] > 300) {
            $_SESSION['error'] = 'Данные авторизации устарели.';
            header('Location: /login');
            exit;
        }

        // Формируем data_check_string
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }

        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);

        // Генерируем секретный ключ
        $secret_key = hash('sha256', TELEGRAM_BOT_TOKEN, true);
        $hash = bin2hex(hash_hmac('sha256', $data_check_string, $secret_key, true));

        // Проверка подписи
        if (strcmp($hash, $check_hash) !== 0) {
            $_SESSION['error'] = 'Неверная подпись данных.';
            header('Location: /login');
            exit;
        }

        // Поиск пользователя по Telegram ID
        $user = User::findByTelegramId($data['id']);

        if ($isAttach) {
            // Привязка Telegram к существующему пользователю
            $current_user = User::find($_SESSION['user_id']);
            if ($current_user) {
                // Проверяем, не привязан ли Telegram ID к другому аккаунту
                if ($user && $user->id !== $current_user->id) {
                    $_SESSION['error'] = 'Этот Telegram уже привязан к другому аккаунту.';
                    header('Location: /profile');
                    exit;
                }

                // Обновляем данные пользователя
                $current_user->telegram_id = $data['id'];
                $current_user->username = $data['username'] ?? '';
                $current_user->first_name = $data['first_name'] ?? '';
                $current_user->last_name = $data['last_name'] ?? '';
                $current_user->avatar = $data['photo_url'] ?? '';

                if ($current_user->save()) {
                    $_SESSION['success'] = 'Telegram успешно привязан!';
                } else {
                    $_SESSION['error'] = 'Ошибка при привязке Telegram.';
                }
            } else {
                $_SESSION['error'] = 'Пользователь не найден.';
            }
        } else {
            // Обычная авторизация через Telegram
            if ($user) {
                // Пользователь найден, авторизуем
                $_SESSION['user_id'] = $user->id;
                $_SESSION['role'] = $user->role;
                $_SESSION['success'] = 'Вы успешно авторизовались через Telegram!';
            } else {
                // Пользователь не найден, создаем нового
                $user = User::createFromTelegram($data);
                if ($user) {
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['role'] = $user->role;
                    $_SESSION['success'] = 'Вы успешно зарегистрировались через Telegram!';
                } else {
                    $_SESSION['error'] = 'Ошибка при регистрации через Telegram.';
                }
            }
        }

        header('Location: /profile');
        exit;
    }

    private function handleEmailLogin($data) {
        $email = $data['email'];
        $password = $data['password'];

        $user = User::findByEmail($email);
        if ($user && $user->verifyPassword($password)) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role'] = $user->role;
            header('Location: /courses');
            exit;
        } else {
            $this->render('auth/login', [
                'error' => 'Invalid email or password',
                'telegram_bot_id' => TELEGRAM_BOT_ID
            ]);
        }
    }

    private function handleEmailRegistration($data) {
        $email = $data['email'];
        $password = $data['password'];
        $confirm_password = $data['confirm_password'];

        if ($password !== $confirm_password) {
            $this->render('auth/register', [
                'error' => 'Passwords do not match'
            ]);
            return;
        }

        $user = User::findByEmail($email);
        if ($user) {
            $this->render('auth/register', [
                'error' => 'User with this email already exists'
            ]);
            return;
        }

        $user = User::createFromEmail($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['role'] = $user->role;
            header('Location: /courses');
            exit;
        } else {
            $this->render('auth/register', [
                'error' => 'Registration failed'
            ]);
        }
    }

    public function attachEmail() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            error_log("Attempting to attach email: " . $email); // Логируем email

            if ($password !== $confirm_password) {
                error_log("Passwords do not match for email: " . $email); // Логируем несовпадение паролей
                $this->render('auth/attach-email', [
                    'error' => 'Пароли не совпадают'
                ]);
                return;
            }

            // Получаем временного пользователя из сессии
            if (!isset($_SESSION['temp_user_id'])) {
                error_log("No temp_user_id found in session"); // Логируем отсутствие temp_user_id
                header('Location: /login');
                exit;
            }

            $temp_user_id = $_SESSION['temp_user_id'];
            error_log("Found temp_user_id in session: " . $temp_user_id); // Логируем temp_user_id

            $user = User::find($temp_user_id);
            if (!$user) {
                error_log("User not found with ID: " . $temp_user_id); // Логируем, если пользователь не найден
                $this->render('auth/attach-email', [
                    'error' => 'Пользователь не найден'
                ]);
                return;
            }

            error_log("User found: " . print_r($user, true)); // Логируем данные пользователя

            if ($user->attachEmail($email, $password)) {
                error_log("Email successfully attached to user ID: " . $user->id); // Логируем успешную привязку email

                // Удаляем временного пользователя из сессии
                unset($_SESSION['temp_user_id']);

                // Авторизуем пользователя
                $_SESSION['user_id'] = $user->id;
                $_SESSION['role'] = $user->role;

                error_log("User authenticated: " . $user->id); // Логируем успешную авторизацию
                header('Location: /courses');
                exit;
            } else {
                error_log("Failed to attach email to user ID: " . $user->id); // Логируем ошибку при привязке email
                $this->render('auth/attach-email', [
                    'error' => 'Ошибка при привязке email'
                ]);
            }
        } else {
            error_log("Rendering attach-email form"); // Логируем отображение формы
            $this->render('auth/attach-email');
        }
    }

    public function attachTelegram() {
        if (!empty($_GET['hash'])) {
            // Обработка данных Telegram
            $this->handleTelegramAuth($_GET, true); // true — флаг для привязки, а не авторизации
        } else {
            header('Location: /profile');
            exit;
        }
    }
}