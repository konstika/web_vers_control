<?php
$projectId = htmlspecialchars($version['id_project'] ?? '');
$versionId = htmlspecialchars($version['id_version_project'] ?? '');
$versionTitle = htmlspecialchars($version['name'] ?? 'Версия без названия');
$versionDescription = htmlspecialchars($version['description'] ?? 'Нет описания.');
?>

<div class="project-list-container">

    <div class="version-header-actions">
        <h1 class="page-title-view">
            Версия: "<?php echo $versionTitle; ?>"
        </h1>
        <div class="version-actions-group">
            <a href="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>/edit"
               class="btn btn-primary btn-icon-text">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Редактировать
            </a>
            <a href="/project/<?php echo $projectId; ?>" class="btn modal-btn-cancel">
                Назад
            </a>
        </div>
    </div>

    <div class="card-panel version-description-panel">
        <h2 class="section-title-project">Описание версии: </h2>
        <p class="project-description-text"><?php echo $versionDescription; ?></p>
    </div>

    <h2 class="section-title-versions">Управление файлами и папками</h2>

    <div class="card-panel file-manager-panel">

        <div class="file-manager-actions">

            <form action="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>/upload-file"
                  method="POST" enctype="multipart/form-data" class="file-upload-form">
                <input type="hidden" name="version_id" value="<?php echo $versionId; ?>">
                <label for="file-upload-input" class="btn btn-primary btn-icon-text">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Загрузить файл(ы)
                </label>
                <input type="file" name="file[]" id="file-upload-input" style="display: none;"
                       onchange="this.form.submit();" multiple>
            </form>

            <form action="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>/upload-file"
                  method="POST" enctype="multipart/form-data" class="file-upload-form">
                <input type="hidden" name="version_id" value="<?php echo $versionId; ?>">
                <label for="dir-upload-input" class="btn btn-primary btn-icon-text">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Загрузить папку
                </label>
                <input type="file" name="file[]" id="dir-upload-input" style="display: none;"
                       onchange="this.form.submit();" multiple webkitdirectory directory>
            </form>
        </div>

        <div class="file-list-container">
            <?php if (empty($files)): ?>
                <div class="empty-state-small">
                    Нет файлов и папок в этой версии.
                </div>
            <?php else: ?>
                <?php foreach ($files as $item): ?>
                    <div class="file-item <?php echo $item['is_dir'] ? 'folder-item' : 'file-item'; ?>">
                        <div class="file-info">
                            <svg class="file-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <?php if ($item['is_dir']): ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                <?php else: ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                <?php endif; ?>
                            </svg>
                            <span class="file-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="file-meta"><?php echo $item['is_dir'] ? 'Папка' : $item['size']; ?></span>
                        </div>

                        <form action="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>/delete-file" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот элемент?');">
                            <input type="hidden" name="item_path" value="<?php echo htmlspecialchars($item['path']); ?>">
                            <button type="submit" class="btn-delete-small">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>

                        <form action="/project/<?php echo $projectId; ?>/version/<?php echo $versionId; ?>/download-file" method="POST">
                            <input type="hidden" name="item_path" value="<?php echo htmlspecialchars($item['path']); ?>">
                            <button type="submit" class="btn-download-small">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </button>
                        </form>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>