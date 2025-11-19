<?php

namespace Model;

use Core\Database;

abstract class Model
{
    protected $conn;

    public function __construct()
    {
        // Инициализируем соединение в базовом классе
        $this->conn = Database::getInstance()->getConnection();
    }
}