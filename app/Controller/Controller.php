<?php

namespace Controller;

abstract class Controller {

    // Загрузка Модели
    protected function model($modelName) {
        $modelClass = "Model\\" . $modelName;
        if (class_exists($modelClass)) {
            return new $modelClass();
        } else {
            die("Модель '$modelName' не найдена.");
        }
    }

    // Загрузка Представления (View)
    protected function view($viewName, $data = []) {
        // Извлекаем переменные из массива $data
        extract($data);
        $viewFile = __DIR__ . '/../View/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            // Подключаем шапку
            require_once __DIR__ . '/../View/templates/header.php';
            // Подключаем сам вид
            require_once $viewFile;
        } else {
            die("Представление '$viewName' не найдено.");
        }
    }
}