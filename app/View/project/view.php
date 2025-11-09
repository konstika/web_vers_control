<?php
$projectId = htmlspecialchars($project['id_project'] ?? '');
$projectTitle = htmlspecialchars($project['name'] ?? 'Проект без названия');
$projectDescription = htmlspecialchars($project['description'] ?? 'Нет описания');
$versions = $versions ?? [];
?>

<div class="project-list-container">

    <div class="project-header-actions">
        <h1 class="page-title-view">Проект: "<?php echo $projectTitle; ?>"</h1>

        <div class="project-actions-group">
            <a href="/project/<?php echo $projectId; ?>/edit"
               class="btn btn-primary btn-icon-text">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Редактировать
            </a>
            <a href="/?>" class="btn modal-btn-cancel">
                Назад
            </a>
        </div>
    </div>

    <div class="card-panel project-description-panel">
        <h2 class="section-title-project">Описание</h2>
        <p class="project-description-text"><?php echo $projectDescription; ?></p>
    </div>

    <div class="versions-header-actions">
        <h2 class="section-title-versions">Версии (<?php echo count($versions); ?>)</h2>
        <a href="/project/<?php echo $projectId; ?>/version/new" class="btn btn-primary">+ Новая версия</a>

    </div>

    <?php if (empty($versions)): ?>
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3>Версии не найдены</h3>
        </div>
    <?php else:?>

        <div class="project-list">
            <?php foreach ($versions as $version):
                $versionPk = htmlspecialchars($version['id_version_project']);
                $versionName = $version['name'] ?? 'Без названия';
                $versionDescription = $version['description'] ?? 'Описание отсутствует.';
                $versionDate = date('d.m.Y', strtotime($version['created_at']));
                ?>

                <div class="version-card">
                    <a href="/project/<?php echo $projectId; ?>/version/<?php echo $versionPk; ?>"
                       class="version-message version-title-link">
                        <?php echo $versionName; ?>
                    </a>

                    <div class="version-description-inline">
                        <span class="version-description-text">
                            <?php echo $versionDescription; ?>
                        </span>
                    </div>

                    <div class="version-date-container">
                        <span class="version-date-text">
                            <?php echo "Дата создания: ".$versionDate; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>