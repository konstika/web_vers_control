<?php

namespace Core;

class Router {
    private $routes = [];

    public function __construct(array $routesConfig) {
        foreach ($routesConfig as $route) {
            if (isset($route[0]) && isset($route[1]) && isset($route[2])) {
                $routeUrl = $route[1];
                if ($routeUrl !== '/' && substr($routeUrl, 0, 1) !== '/') {
                    $routeUrl = '/' . $routeUrl;
                }
                $this->routes[] = [
                    'method' => $route[0],
                    'route' => '#^' . str_replace('/', '\/', $routeUrl) . '$#',
                    'controllerAction' => $route[2],
                ];
            }
        }
    }

    // Обработка запроса
    public function dispatch() {
        $url = $this->getUrl();
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['route'], $url, $matches)) {

                // Убираем полное совпадение
                array_shift($matches);

                // Разбираем 'Controller@method'
                list($controllerName, $actionName) = explode('@', $route['controllerAction']);

                $controllerClass = "Controller\\" . $controllerName;
                if (!class_exists($controllerClass)) {
                    die("Контроллер '$controllerClass' не найден.");
                }

                $controller = new $controllerClass();
                if (!method_exists($controller, $actionName)) {
                    die("Метод '$actionName' в контроллере '$controllerClass' не найден.");
                }

                // Вызываем метод контроллера, передавая параметры из URL
                call_user_func_array([$controller, $actionName], $matches);
                return;
            }
        }

        // Если ни один маршрут не подошел
        die("Маршрут не найден.");
        http_response_code(404);
        echo "404 - Страница не найдена";
    }

    // Получаем URL из параметра ?url=
    private function getUrl() {
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
        if (empty($url)) {return '/';}
        return '/' . $url;
    }
}