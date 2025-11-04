<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>VCS App</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        nav { background: #f0f0f0; padding: 10px; margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; }
        .error { color: red; }
    </style>
</head>
<body>

<nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/logout">Выход</a>
    <?php else: ?>
        <a href="/login">Вход</a>
        <a href="/register">Регистрация</a>
    <?php endif; ?>
</nav>

<main>