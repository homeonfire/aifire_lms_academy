-- Создание таблицы уведомлений
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'system',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `notifications_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Добавляем несколько тестовых уведомлений для демонстрации
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `data`, `is_read`) VALUES
(1, 'system', 'Добро пожаловать!', 'Добро пожаловать в нашу образовательную платформу!', '{"action": "welcome"}', 0),
(1, 'new_message', 'Новое сообщение', 'У вас есть новое сообщение от администратора', '{"sender_id": 1, "message_preview": "Привет! Как дела с обучением?"}', 0),
(1, 'homework', 'Новое домашнее задание', 'Добавлено новое домашнее задание к уроку "Основы PHP"', '{"lesson_id": 1, "lesson_title": "Основы PHP"}', 0); 