<h2>Вход</h2>

<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="/login">
    <div>
        <label for="login">Логин:</label>
        <input type="text" id="login" name="login" required>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <br>
    <button type="submit">Войти</button>
</form>