<?php
namespace Model;
use Core\Database;
use \PDO;
class Project
{
    private $conn;

    public function __construct() {
       $this->conn = Database::getInstance()->getConnection();
    }

    public function getProjectsByUserId(int $userId): array {
        $sql = "SELECT id_project, name, description, created_at
                FROM project
                WHERE created_by = :created_by
                ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':created_by', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProjectById(int $id_project) {
        $sql = "SELECT id_project, name, description, path, created_by, created_at 
                FROM project 
                WHERE id_project = :id_project";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_project', $id_project, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createProject(int $userId, array $data): bool {
        $sql = "INSERT INTO project (created_by, name, path, description) 
                VALUES (:created_by, :name, :path, :description)";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':created_by' => $userId,
            ':name' => $data['name'] ?? '',
            ':path' => $data['path'] ?? '',
            ':description' => $data['description'] ?? ''
        ]);
        return $success;
    }

    public function updateProject(int $id, array $data): bool {
        $sql = "UPDATE project
                SET name = :name, description = :description
                WHERE id_project = :id_project";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'] ?? '',
            ':description' => $data['description'] ?? '',
            ':id_project' => $id
        ]);
    }

    public function deleteProject(int $id): bool {
        $sql = "DELETE FROM project WHERE id_project = :id_project";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id_project' => $id]);
    }
}
