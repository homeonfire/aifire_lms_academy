-- Миграция: добавление поля course_id_access в таблицу subscriptions
-- Это поле будет указывать, к какому курсу предоставляется доступ

-- Добавляем поле course_id_access
ALTER TABLE subscriptions ADD COLUMN course_id_access INT NULL AFTER user_id;

-- Добавляем внешний ключ для связи с курсами
ALTER TABLE subscriptions ADD CONSTRAINT subscriptions_course_id_access_fk 
    FOREIGN KEY (course_id_access) REFERENCES courses(id) ON DELETE CASCADE;

-- Добавляем индекс для быстрого поиска по курсу
ALTER TABLE subscriptions ADD INDEX idx_course_id_access (course_id_access);

-- Обновляем комментарий к таблице
ALTER TABLE subscriptions COMMENT = 'Подписки пользователей на курсы. course_id_access указывает на конкретный курс, NULL означает общую подписку'; 