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

define('PROJECT_STORAGE_ROOT', __DIR__ . '/../../project_storage/');

//Определение маршрутов
$routes = [
    // [Метод, URL (Regex), 'Контроллер@Метод']

    // Аутентификация
    ['GET', '/register', 'UserController@showRegister'],
    ['POST', '/register', 'UserController@register'],
    ['GET', '/login', 'UserController@showLogin'],
    ['POST', '/login', 'UserController@login'],
    ['GET', '/logout', 'UserController@logout'],

    // Страница с проектами
    ['GET', '/', 'ProjectController@index'],
    ['GET', '/project/new', 'ProjectController@new'],
    ['POST', '/project/create', 'ProjectController@create'],
    ['GET', '/project/(\d+)', 'ProjectController@show'],
    ['GET', '/project/(\d+)/edit', 'ProjectController@edit'],
    ['POST', '/project/(\d+)/update', 'ProjectController@update'],
    ['POST', '/project/(\d+)/delete', 'ProjectController@delete'],

    // Управление версиями
    ['GET', '/project/(\d+)/version/new', 'VersionProjectController@new'],
    ['POST', '/project/(\d+)/version/create', 'VersionProjectController@create'],
    ['GET', '/project/(\d+)/version/(\d+)', 'VersionProjectController@show'],
    ['POST', '/project/(\d+)/version/(\d+)/upload-file', 'VersionProjectController@upload'],
    ['POST', '/project/(\d+)/version/(\d+)/download-all', 'VersionProjectController@downloadAll'],
    ['GET', '/project/(\d+)/version/(\d+)/edit', 'VersionProjectController@edit'],
    ['POST', '/project/(\d+)/version/(\d+)/update', 'VersionProjectController@update'],
    ['POST', '/project/(\d+)/version/(\d+)/delete', 'VersionProjectController@delete'],
    ['POST', '/project/(\d+)/version/(\d+)/delete-file', 'VersionProjectController@deleteFile'],
];
$router = new Core\Router($routes);
$router->dispatch();