<?php
$projectId = htmlspecialchars($project['id_project'] ?? '');
$projectTitle = htmlspecialchars($project['name'] ?? '');
$projectDescription = htmlspecialchars($project['description'] ?? '');
?>

<div class="container-centered">
    <h1 class="page-title">Редактировать проект: "<?php echo $projectTitle; ?>"</h1>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <div class="card-panel">
        <form method="POST" action="/project/<?php echo $projectId; ?>/update">
            <div class="form-group">
                <label for="name" class="form-label">Название проекта:</label>
                <input type="text" id="title" name="name" class="form-input"
                       value="<?php echo $projectTitle; ?>" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Описание проекта (необязательно):</label>
                <textarea id="description" name="description" class="form-textarea" rows="4"
                          placeholder="Краткое описание целей и задач проекта"><?php echo $projectDescription; ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full">Сохранить изменения</button>
            <a href="/project/<?php echo $projectId; ?>" class="btn modal-btn-cancel">Отмена</a>
        </form>

        <!-- Удаление проекта -->
        <div class="danger-zone">
            <p>Удаление проекта необратимо. Будут потеряны все данные о версиях и метаданные проекта.</p>
            <form id="deleteForm" method="POST" action="/project/<?php echo $projectId; ?>/delete"
                  onsubmit="return confirmDelete(event)">
                <button type="submit" class="btn btn-delete">Удалить проект</button>
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
                <h3 class="modal-title">Подтверждение удаления</h3>
                <p class="modal-body-text">Вы уверены, что хотите удалить проект "<?php echo $projectTitle; ?>"? Это действие необратимо.</p>
                <div class="modal-footer">
                    <button id="cancelDelete" class="btn modal-btn-cancel" type="button">
                        Отмена
                    </button>
                    <button id="confirmDelete" class="btn btn-delete modal-btn-confirm" type="button">
                        Удалить
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(dialog);
        const closeDialog = function() {
            document.body.removeChild(dialog);
            document.body.style.overflow = '';
        };
        document.getElementById('cancelDelete').onclick = closeDialog;
        document.getElementById('confirmDelete').onclick = function() {
            closeDialog();
            document.getElementById('deleteForm').submit();
        };
        return false;
    }
</script>
