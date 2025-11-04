<?php

namespace Controller;

class UserController extends Controller {

    // Показать форму регистрации
    public function showRegister() {
        $this->view('user/register');
    }

    // Обработать регистрацию
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: /register');
            return;
        }

        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        if (empty($login) || empty($password)) {
            $this->view('user/register', ['error' => 'Логин и пароль не могут быть пустыми']);
            return;
        }

        if ($password !== $password_confirm) {
            $this->view('user/register', ['error' => 'Пароли не совпадают']);
            return;
        }

        $userModel = $this->model('User');

        if ($userModel->findByLogin($login)) {
            $this->view('user/register', ['error' => 'Этот логин уже занят']);
            return;
        }

        $userId = $userModel->create($login, $password);
        if ($userId) {
            header('Location: /login');
        } else {
            $this->view('user/register', ['error' => 'Ошибка при регистрации']);
        }
    }

    // Показать форму входа
    public function showLogin() {
        $this->view('user/login');
    }

    // Обработать вход
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: /login');
            return;
        }

        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = $this->model('User');
        $user = $userModel->verifyPassword($login, $password);

        if ($user) {//Успешный вход
            // Сохранение пользователя в сессии
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_login'] = $user['login'];

            // Перенаправление к главной (проекты)
            header('Location: /');
        } else {
            // Ошибка входа
            $this->view('user/login', ['error' => 'Неверный логин или пароль']);
        }
    }

    // Выход
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /login');
    }
}