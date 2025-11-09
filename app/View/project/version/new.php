<?php
$projectId = htmlspecialchars($id_project ?? '');
$projectName = htmlspecialchars($project_name ?? 'Проект без названия');
$nameValue = htmlspecialchars($name ?? '');
$descriptionValue = htmlspecialchars($description ?? '');
?>

<div class="container-centered">
    <h1 class="page-title">Новая версия для проекта: "<?php echo $projectName; ?>"</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="card-panel">
        <form method="POST" action="/project/<?php echo $projectId; ?>/version/create">
            <div class="form-group">
                <label for="name" class="form-label">Название версии:</label>
                <input type="text" id="name" name="name" class="form-input"
                       value="<?php echo $nameValue; ?>" required
                       placeholder="Название версии, например: 'Веб-приложение VCS v.1.1'">
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Описание изменений (необязательно):</label>
                <textarea id="description" name="description" class="form-textarea" rows="4"
                          placeholder="Описание, что было сделано в этой версии"><?php echo $descriptionValue; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full">Создать версию</button>
            <a href="/project/<?php echo $projectId; ?>" class="btn modal-btn-cancel">Отмена</a>
        </form>
    </div>
</div>
