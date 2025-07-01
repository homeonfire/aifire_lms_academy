<?php
class Courses extends Model {
    protected static $courses = "courses";
    protected static $modules = "modules";
    protected static $lessons = "lessons";

    public $id;
    public $title;
    public $description;
    public $author_id;
    public $created_at;
    public $order_number;
    public $course_id;
    public $content;
    public $module_id;
    public $title_course;
    public $c_description;
    public $emojis;

    // ------------------------------- ФУНКЦИИ ОТОБРАЖЕНИЯ --------------------------------------------- //
    public static function showCourses()
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("SELECT * FROM ".self::$courses);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage()); // Логируем ошибки БД
            return false;
        }
    }

    public static function showModules($moduleId)
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("SELECT modules.*, courses.`title` as `title_course`, courses.description as `c_description`, courses.id as `course_id` FROM modules INNER JOIN courses ON (modules.course_id=courses.id) WHERE course_id=".$moduleId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }

    public static function showLessons($lessonId)
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("SELECT lessons.*, modules.`title` as `title_course`, modules.description as `c_description` FROM lessons INNER JOIN modules ON (lessons.module_id=modules.id) WHERE lessons.module_id=".$lessonId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }

    public static function getLessonById($lesson_id) {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("SELECT * FROM lessons WHERE id = :lesson_id");
            $stmt->execute([
                ':lesson_id' => $lesson_id
            ]);

            // Возвращаем объект урока
            return $stmt->fetchObject();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    // ------------------------------- ФУНКЦИИ ОТОБРАЖЕНИЯ --------------------------------------------- //


    // ------------------------------- ФУНКЦИИ ДОБАВЛЕНИЯ --------------------------------------------- //
    // Сохранение урока
    public static function saveLesson($module_id, $title, $order_number, $elements)
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("INSERT INTO lessons (title, content, order_number, module_id) VALUES (:title, :content, :order_number, :module_id)");

            // Логируем данные
            error_log("Saving lesson: title=$title, order_number=$order_number, module_id=$module_id");

            $stmt->execute([
                ':title' => $title,
                ':content' => json_encode($elements), // Сохраняем элементы урока как JSON
                ':order_number' => $order_number,
                ':module_id' => $module_id
            ]);

            return $db->lastInsertId(); // Возвращаем ID нового урока
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public static function addCourse($title, $description, $emojis, $author_id)
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("INSERT INTO courses (title, description, emojis, author_id) VALUES (:title, :description, :emojis, :author_id)");

            //Логирование данных
            error_log("Сохранение курса: " . $title);

            $stmt->execute([
                ':title' => $title,
                ':description' => $description,
                ':emojis' => $emojis,
                ':author_id' => $author_id
            ]);

            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public static function addModule($course_id, $title, $description, $emojis, $order_id) {
        error_log("Module: Стартанул модуль");
        try {
            $db = DB::getInstance();
            $stmt = $db->prepare("INSERT INTO modules (course_id, title, description, emojis, order_number) VALUES (:course_id, :title, :description, :emojis, :order_number)");

            error_log("Сохранение модуля: ". $course_id . " " .$title . " " . $description . " " . $emojis . " " .$course_id);

            $stmt->execute([
                ':course_id' => $course_id,
                ':title' => $title,
                ':description' => $description,
                ':emojis' => $emojis,
                ':order_number' => $order_id
            ]);

            return $db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    // ------------------------------- ФУНКЦИИ ДОБАВЛЕНИЯ --------------------------------------------- //
}