<?php
$versionId = htmlspecialchars($version['id_version_project'] ?? '');
$projectId = htmlspecialchars($version['id_project'] ?? '');
$versionName = htmlspecialchars($version['name'] ?? '');
$versionDescription = htmlspecialchars($version['description'] ?? '');
$projectName = htmlspecialchars($projectName ?? 'Проект');
?>

<div class="container-centered">
    <h1 class="page-title">Редактировать версию: "<?php echo $versionName; ?>"</h1>

    <p class="text-link" style="text-align: center; margin-top: -10px; margin-bottom: 15px;">
        Проект: <a href="/project/<?php echo $projectId; ?>" style="color: #4f46e5;"><?php echo $projectName; ?></a>
    </p>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="card-panel">
        <form method="POST" action="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>/update">
            <div class="form-group">
                <label for="name" class="form-label">Название версии:</label>
                <input type="text" id="name" name="name" class="form-input"
                       value="<?php echo $versionName; ?>" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Описание изменений (необязательно):</label>
                <textarea id="description" name="description" class="form-textarea" rows="4"
                          placeholder="Детальное описание внесенных изменений"><?php echo $versionDescription; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full">Сохранить изменения</button>
            <a href="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>" class="btn modal-btn-cancel">Отмена</a>
        </form>

        <div class="danger-zone">
            <p>Удаление этой версии необратимо. Будут потеряны все привязанные файлы и метаданные версии.</p>
            <form id="deleteForm" method="POST" action="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>/delete"
                  onsubmit="return confirmDelete(event)">
                <button type="submit" class="btn btn-delete">Удалить версию</button>
            </form>
        </div>
    </div>
</div>

<script>
    //Окно подтверждения удаления
    function confirmDelete(event) {
        event.preventDefault();
        document.body.style.overflow = 'hidden';
        const dialog = document.createElement('div');
        dialog.className = 'modal-overlay';
        dialog.innerHTML = `
            <div class="modal-content">
                <h3 class="modal-title">Под