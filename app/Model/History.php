<?php
namespace Model;

use \PDO;

class History extends Model
{
    //создание новую запись в таблице history.
    public function createHistoryRecord(int $projectId, int $versionId = null, string $description, int $userId): bool
    {
        $sql = "INSERT INTO history (id_project, id_version_project, description, created_by)
                VALUES (:id_project, :id_version_project, :description, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $versionId = $versionId ?? null;

        return $stmt->execute([
            ':id_project' => $projectId,
            ':id_version_project' => $versionId,
            ':description'=> $description,
            ':created_by' => $userId,
        ]);
    }

    //Получение всей истории проекта
    public function getProjectHistory(int $projectId): array
    {
        $sql = "SELECT h.*, p.name AS project_name, vp.name AS version_name
                FROM history h
                JOIN project p ON h.id_project = p.id_project
                LEFT JOIN version_project vp ON h.id_version_project = vp.id_version_project
                WHERE h.id_project = :id_project 
                ORDER BY h.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_project', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //Получение истории конкретной версии.
    public function getVersionHistory(int $projectId, int $versionId): array
    {
        $sql = "SELECT h.*, p.name AS project_name, vp.name AS version_name
                FROM history h
                JOIN project p ON h.id_project = p.id_project
                JOIN version_project vp ON h.id_version_project = vp.id_version_project
                WHERE h.id_project = :id_project AND h.id_version_project = :id_version_project
                ORDER BY h.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_project', $projectId, PDO::PARAM_INT);
        $stmt->bindParam(':id_version_project', $versionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}