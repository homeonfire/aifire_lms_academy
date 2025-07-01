<?php
class Router {
    protected $routes = [];

    public function add($route, $params) {
        $this->routes[$route] = $params;
    }

    public function dispatch($url) {
        $url = trim($url, '/');

        foreach ($this->routes as $route => $params) {
            if ($url === $route) {
                $controller = $params['controller'];
                $action = $params['action'];

                $controller_file = "app/controllers/$controller.php";

                if (file_exists($controller_file)) {
                    require_once $controller_file;
                    $controller_instance = new $controller();
                    $controller_instance->$action();
                }
                return;
            }
        }
        http_response_code(404);
        echo 'Page not found';
    }
}