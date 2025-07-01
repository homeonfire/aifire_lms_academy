<?php
require_once "app/core/Controller.php";

class CoursesController extends Controller {

    //Главная страница курсов, выводим все курсы
    public function index()
    {
        $this->checkAuth();

        $allCourses = Courses::showCourses();
        $this->render('courses/index', [
            'allCourses' => $allCourses
        ]);
    }

    public function modules() {
        $this->checkAuth();
        $module_id = $_GET['mId'];

        $allModules = Courses::showModules($module_id);
        $this->render('courses/modules', [
            'allModules' => $allModules
        ]);
    }

    public function lessons() {
        $this->checkAuth();
        $lesson_id = $_GET['lId'];

        $allLessons = Courses::showLessons($lesson_id);
        $this->render('courses/lessons', [
            'allLessons' => $allLessons
        ]);
    }

    // Форма создания урока
    public function create() {
        $this->checkAuth();
        $module_id = $_GET['module_id']; // Получаем module_id из запроса
        $this->render('courses/create', [
            'module_id' => $module_id
        ]);
    }

    public function createCourse() {
        $this->checkAuth();

        $this->render('courses/createCourse');
    }

    public function createModule() {
        $this->checkAuth();
        $course_id = $_GET['course_id'];

        $this->render('courses/createModule', [
            'course_id' => $course_id
        ]);
    }

    // Сохранение урока
    public function storeLesson() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Устанавливаем заголовок JSON
            header('Content-Type: application/json');
            $posts = json_decode(file_get_contents('php://input'), true);

            // Проверяем, что все необходимые данные переданы
            if (empty($posts['title'])) {
                echo json_encode(['status' => 'error', 'message' => 'Название урока не может быть пустым.']);
                return;
            }

            $module_id = $posts['module_id'];
            $title = $posts['title'];
            $order_number = $posts['order_number'];
            $elements = $posts['elements']; // JSON с элементами урока

            // Логируем данные для отладки
            error_log("Module ID: $module_id");
            error_log("Title: $title");
            error_log("Order Number: $order_number");
            error_log("Elements: " . print_r($elements, true));

            // Сохраняем урок через модель
            $result = Courses::saveLesson($module_id, $title, $order_number, $elements);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Урок успешно сохранен!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Ошибка при сохранении урока.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Недопустимый метод запроса.']);
        }
    }

    public function storeCourse()
    {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $posts = json_decode(file_get_contents('php://input'), true);

            $title = $posts['title'];
            $emojis = $posts['emojis'];
            $description = $posts['description'];
            $author_id = $posts['author_id'];

            $result = Courses::addCourse($title, $description, $emojis, $author_id);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Курс успешно создан']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Ошибка при сохранении курса']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Недопустимый метод запроса']);
        }
    }

    public function storeModule() {
        $this->checkAuth();
        error_log("Controller: Начали сохранять");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $posts = json_decode(file_get_contents('php://input'), true);
            error_log("Controller: получили POST Запрос:" . print_r($posts, true));

            $title = $posts['title'];
            $emojis = $posts['emojis'];
            $description = $posts['description'];
            $order_number = $posts['order_number'];
            $course_id = $posts['courseId'];

            error_log("Сохранение модуля Controller: ". $course_id . " " .$title . " " . $description . " " . $emojis . " " .$course_id);
            $result = Courses::addModule($course_id, $title, $description, $emojis, $order_number);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Курс успешно создан']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Ошибка при сохранении курса']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Недопустимый метод запроса']);
        }
    }

    public function showLesson() {
        $this->checkAuth();

        // Получаем lesson_id из GET-запроса
        $lesson_id = $_GET['lesson_id'];

        // Получаем данные урока из модели
        $lesson = Courses::getLessonById($lesson_id);

        if ($lesson) {
            // Преобразуем JSON-данные в массив
            $lesson->content = json_decode($lesson->content, true);

            // Отображаем страницу урока
            $this->render('courses/showLesson', [
                'lesson' => $lesson
            ]);
        } else {
            // Если урок не найден, выводим ошибку
            $this->render('errors/404');
        }
    }
}