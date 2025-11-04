<h2>Регистрация</h2>

<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="/register">
    <div>
        <label for="login">Логин:</label>
        <input type="text" id="login" name="login" required>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <label for="password_confirm">Повторите пароль:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
    </div>
    <br>
    <button type="submit">Зарегистрироваться</button>
</form>