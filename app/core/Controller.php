<?php
abstract class Controller {
    protected function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    protected function render($view, $data = []) {
        extract($data);
        $view_file = "views/$view.php";

        if (file_exists($view_file)) {
            require_once $view_file;
        } else {
            throw new Exception("View $view not found");
        }
    }
}