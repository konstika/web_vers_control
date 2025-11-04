<?php
//Точка входа
session_start();
// Автозагрузка классов
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Конфигурация БД
define('DB_HOST', 'db');
define('DB_NAME', 'vers_control_DB');
define('DB_USER', 'user');
define('DB_PASS', 'password');

//Определение маршрутов
$routes = [
    // [Метод, URL (Regex), 'Контроллер@Метод']

    // Главная
    ['GET', '/', 'ProjectController@index'],

    // Аутентификация
    ['GET', '/register', 'UserController@showRegister'],
    ['POST', '/register', 'UserController@register'],
    ['GET', '/login', 'UserController@showLogin'],
    ['POST', '/login', 'UserController@login'],
    ['GET', '/logout', 'UserController@logout'],

    // (Будущие) Маршруты для проектов
    // ['GET', '/project/new', 'ProjectController@create'],
    // ['POST', '/project/store', 'ProjectController@store'],
    // ['GET', '/project/(\d+)', 'ProjectController@show'], // (\d+) - id
];
$router = new Core\Router($routes);
$router->dispatch();