<div class="container-centered">
    <h2 class="page-title">Регистрация</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="card-panel">
        <form method="POST" action="/register">
            <div class="form-group">
                <label for="login" class="form-label">Логин:</label>
                <input type="text" id="login" name="login" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Пароль:</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            <div class="form-group">
                <label for="password_confirm" class="form-label">Повторите пароль:</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-input" required>
            </div>
            <br>
            <button type="submit" class="btn btn-primary w-full">Зарегистрироваться</button>
        </form>
    </div>
</div>