<?php

namespace Controller;

class ProjectController  extends Controller {

    // Главная страница (отображение проектов)
    public function index() {
        // Проверка авторизации
        if (!isset($_SESSION['user_id'])) {
            // Если не авторизован, перенаправляем на страницу входа
            header('Location: /login');
            exit;
        }

        $data = [
            'login' => $_SESSION['user_login'],
            'projects' => [] // В будущем здесь будут загружаться проекты
        ];

        // Используем новое представление project/index.php
        $this->view('project/index', $data);
    }
}