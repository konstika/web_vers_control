<div class="container-centered">
    <h1 class="page-title">Создать новый проект</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="card-panel">
        <form method="POST" action="/project/create">
            <div class="form-group">
                <label for="title" class="form-label">Название проекта:</label>
                <input type="text" id="title" name="name" class="form-input"
                       value="<?php echo htmlspecialchars($name ?? ''); ?>" required
                       placeholder="Например: 'Веб-приложение VCS'">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Описание проекта (необязательно):</label>
                <textarea id="description" name="description" class="form-textarea" rows="4"
                          placeholder="Краткое описание целей и задач проекта"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full">Создать проект</button>
            <a href="/" class="btn modal-btn-cancel">Отмена</a>
        </form>
    </div>
</div>