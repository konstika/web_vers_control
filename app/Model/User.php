<?php

namespace Model;

use Core\Database;
use PDO;

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Поиск пользователя по логину
    public function findByLogin($login) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Создание пользователя
    public function create($login, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $this->conn->prepare("INSERT INTO user (login, password) VALUES (?, ?)");
            $stmt->execute([$login, $hashedPassword]);
            return $this->conn->lastInsertId();
        } catch (\PDOException $e) {
            return false;
        }
    }

    // Проверка логина и пароля
    public function verifyPassword($login, $password) {
        $user = $this->findByLogin($login);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}