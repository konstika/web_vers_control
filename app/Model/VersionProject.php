<?php
namespace Model;

use Core\Database;
use \PDO;

class VersionProject
{
    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    //Получение всех версий проекта
    public function getVersionsByProjectId(int $projectId): array
    {
        $sql = "SELECT * FROM version_project 
                WHERE id_project = :id_project 
                ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_project', $projectId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   //Версия проекта по его id
    public function getVersionById(int $versionId, int $projectId)
    {
        $sql = "SELECT * FROM version_project 
                WHERE id_version_project = :version_id AND id_project = :id_project";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':version_id', $versionId, PDO::PARAM_INT);
        $stmt->bindParam(':id_project', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Создание новой версии проекта
    public function createVersion(int $userId, array $data)
    {
        $sql = "INSERT INTO version_project (id_project, name, path, description, created_by)
                VALUES (:id_project, :name, :path, :description, :created_by)";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':id_project' => $data['id_project'] ?? 0,
            ':name'       => $data['name'] ?? '',
            ':path'       => $data['path'] ?? '',
            ':description'=> $data['description'] ?? null,
            ':created_by' => $userId,
        ]);
        return $success ? (int)$this->conn->lastInsertId() : false;
    }

    //Обновление информации о версии
    public function updateVersion(int $versionId, array $data): bool
    {
        $sql = "UPDATE version_project SET name = :name, description = :description WHERE id_version_project = :version_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':description' => $data['description'] ?? null,
            ':version_id' => $versionId
        ]);
    }


    //Удаление версии проекта из базы данных
    public function deleteVersion(int $versionId, int $projectId): bool
    {
        $sql = "DELETE FROM version_project WHERE id_version_project = :version_id AND id_project = :id_project";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':version_id' => $versionId,
            ':id_project' => $projectId
        ]);
    }
}