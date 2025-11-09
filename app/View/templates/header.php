<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>VCS App</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/">Проекты</a> <a href="/logout">Выход</a>
    <?php else: ?>
        <a href="/login">Вход</a>
        <a href="/register">Регистрация</a>
    <?php endif; ?>
</nav>

<main>