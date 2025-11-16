<?php
$historyData = $data['historyData'] ?? [];
$isProjectView = $data['isProjectView'] ?? false;
?>

<div class="card-panel" style="margin-bottom: 2rem;">
    <h2 class="section-title-project">
        <?php echo $isProjectView ? 'История изменений проекта' : 'История изменений версии'; ?>
    </h2>
    <div class="history-list">
        <?php if (!empty($historyData)): ?>
            <?php foreach ($historyData as $entry): ?>
                <div class="history-item">
                    <span class="history-item-description">
                        <?php
                        $context = '';
                        if ($isProjectView && !empty($entry['version_name'])) {
                            $context = "<span class=\"history-item-context\">Версия \"".htmlspecialchars($entry['version_name'])."\":</span>";
                        }
                        echo $context;
                        echo htmlspecialchars($entry['description'] ?? 'Действие без описания');
                        ?>
                    </span>

                    <span class="history-item-date">
                        <?php echo date('d.m.Y H:i', strtotime($entry['created_at'] ?? time())); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Действий в истории <?php echo $isProjectView ? 'проекта' : 'этой версии'; ?> нет.</p>
        <?php endif; ?>
    </div>
</div>