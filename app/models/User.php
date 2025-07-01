<?php
class User extends Model {
    protected static $table = 'users';

    public $id;
    public $telegram_id;
    public $username;
    public $first_name;
    public $last_name;
    public $avatar;
    public $created_at;
    public $role;
    public $access_to_courses;
    public $email;
    public $password_hash;

    public static function createFromTelegram($data) {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("INSERT INTO users 
            (telegram_id, username, first_name, last_name, avatar, created_at, role) 
            VALUES (?, ?, ?, ?, ?, NOW(), ?)");

            $params = [
                $data['id'],
                isset($data['username']) ? $data['username'] : '',
                isset($data['first_name']) ? $data['first_name'] : '',
                isset($data['last_name']) ? $data['last_name'] : '',
                isset($data['photo_url']) ? $data['photo_url'] : '',
                'user'
            ];

            error_log("SQL Params: " . print_r($params, true)); // Логируем параметры

            $stmt->execute($params);
            error_log("Last insert ID: " . $db->lastInsertId()); // Логируем ID новой записи

            return self::find($db->lastInsertId());
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Логируем ошибки БД
            return false;
        }
    }

    public function updateAvatar($avatar_url) {
        $db = DB::getInstance();
        $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([$avatar_url, $this->id]);
    }

    public static function findByTelegramId($telegram_id) {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE telegram_id = ?");
        $stmt->execute([$telegram_id]);
        $result = $stmt->fetchObject(get_called_class());

        error_log("Find user result: " . ($result ? "Found" : "Not found"));

        return $result;
    }

    public static function createFromEmail($email, $password) {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("INSERT INTO users 
            (email, password_hash, created_at, role) 
            VALUES (?, ?, NOW(), ?)");

            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $params = [
                $email,
                $password_hash,
                'user'
            ];

            error_log("SQL Params: " . print_r($params, true)); // Логируем параметры

            $stmt->execute($params);
            error_log("Last insert ID: " . $db->lastInsertId()); // Логируем ID новой записи

            return self::find($db->lastInsertId());
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Логируем ошибки БД
            return false;
        }
    }

    /**
     * Поиск пользователя по email.
     */
    public static function findByEmail($email) {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetchObject(get_called_class());

        error_log("Find user by email result: " . ($result ? "Found" : "Not found"));

        return $result;
    }

    /**
     * Проверка пароля.
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->password_hash);
    }


    /**
     * Поиск пользователя по ID.
     */
    public static function find($id) {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchObject(get_called_class());
    }

    /**
     * Привязка email и пароля к существующему пользователю.
     */
    public function attachEmail($email, $password) {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("UPDATE users SET email = ?, password_hash = ? WHERE id = ?");

            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $params = [$email, $password_hash, $this->id];

            error_log("Executing SQL query with params: " . print_r($params, true)); // Логируем параметры запроса

            $stmt->execute($params);

            error_log("Email attached successfully for user ID: " . $this->id); // Логируем успешное выполнение
            return true;
        } catch (PDOException $e) {
            error_log("Database error in attachEmail: " . $e->getMessage()); // Логируем ошибку базы данных
            return false;
        }
    }

    public function save() {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("UPDATE users SET 
            telegram_id = ?, 
            username = ?, 
            first_name = ?, 
            last_name = ?, 
            avatar = ? 
            WHERE id = ?");

            $stmt->execute([
                $this->telegram_id,
                $this->username,
                $this->first_name,
                $this->last_name,
                $this->avatar,
                $this->id
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Database error in save: " . $e->getMessage());
            return false;
        }
    }
}