<div class="project-list-container">
    <div class="project-header">
        <h1 class="page-title" style="text-align: left; margin-bottom: 0;">Мои проекты</h1>
        <a href="/project/new"
           class="btn btn-primary">
            + Создать новый проект
        </a>
    </div>

    <?php if (empty($projects)): ?>
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3>Проекты не найдены</h3>
        </div>
    <?php else: ?>
        <div class="project-list">
            <?php foreach ($projects as $project):
                $projectId = htmlspecialchars($project['id_project'] ?? '');
                $projectTitle = htmlspecialchars($project['name'] ?? 'Без названия');
                $createdAt = date('d.m.Y', strtotime($project['created_at'] ?? ''));
            ?>
                <div class="project-card">

                    <div class="flex-grow min-w-0 pr-4">
                        <a href="/project/<?= $projectId ?>"
                           class="project-title-link">
                            <?= $projectTitle ?>
                        </a>
                    </div>

                    <div class="project-meta">
                        <span class="project-date">
                            Создан: <?= $createdAt ?>
                        </span>

                        <a href="/project/<?= $projectId ?>/edit"
                           class="btn-edit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            <span>Редактировать</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>