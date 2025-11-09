<?php
namespace Controller;
class ProjectController  extends Controller {

    //Получение списка проектов пользователя и отображение
    public function index() {
        $userId = $this->authenticate();
        $projectModel = $this->model('Project');
        $projects = $projectModel->getProjectsByUserId($userId);
        $this->view('project/list', ['projects' => $projects]);
    }


     //Отображение формы для создания проектов
    public function new() {
        $this->authenticate();
        $data = [
            'name' => '',
            'description' => '',
        ];
        $this->view('project/new', $data);
    }

    //Обработка POST-запроса для сохранения нового проекта
    public function create() {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }
        $projectModel = $this->model('Project');
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];
        if (empty($data['name'])) {
            $error = "Название проекта не может быть пустым.";
            $this->view('project/new', ['error' => $error, 'name' => $data['name'], 'description' => $data['description']]);
            return;
        }
        //Создание директории проекта
        $projectDirName = $userId . '_' . uniqid();
        $projectPath = PROJECT_STORAGE_ROOT . $projectDirName;
        if (!mkdir($projectPath, 0777, true)) {
            $error = "Не удалось создать директорию проекта. Проверьте права доступа.";
            $this->view('project/new', ['error' => $error, 'name' => $data['name'], 'description' => $data['description']]);
            return;
        }
        $data['path'] = $projectPath;

        if ($projectModel->createProject($userId, $data)) {
            header('Location: /'); // Перенаправление на список проектов
        } else {
            // В случае ошибки БД, удаляем созданную директорию
            if (is_dir($projectPath)) {
                rmdir($projectPath);
            }
            $error = "Не удалось создать проект. Ошибка базы данных.";
            $this->view('project/new', ['error' => $error, 'name' => $data['name'], 'description' => $data['description']]);
        }
        exit;
    }

    //Отображение страницы редактирования проекта
    public function edit(int $id_project) {
        $userId = $this->authenticate();
        $projectModel = $this->model('Project');
        $project = $projectModel->getProjectById($id_project);
        if (!$project || $project['created_by'] !== $userId) {
            http_response_code(404);
            $this->view('404');
            exit;
        }
        $this->view('project/edit', ['project' => $project]);
    }

    //Обработка POST-запроса для обновления проекта
    public function update(int $id_project) {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }
        $projectModel = $this->model('Project');

        $project = $projectModel->getProjectById($id_project);

        if (!$project || $project['created_by'] !== $userId) {
            http_response_code(403);
            die("Нет доступа.");
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        if (empty($data['name'])) {
            $error = "Название проекта не может быть пустым.";
            // Возвращаем на форму редактирования с ошибкой
            $this->view('project/edit', ['error' => $error, 'project' => array_merge($project, $data)]);
            return;
        }

        if ($projectModel->updateProject($id_project, $data)) {
            header('Location: /');
        } else {
            header("Location: /project/{$id_project}/edit");
        }
        exit;
    }

    //Обрабатка POST-запроса для удаления проекта
    public function delete(int $id_project) {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }
        $projectModel = $this->model('Project');
        $project = $projectModel->getProjectById($id_project);
        if (!$project || $project['created_by'] !== $userId) {
            http_response_code(403);
            die("Нет доступа к удалению.");
        }
        if ($projectModel->deleteProject($id_project)) {
            header('Location: /');
        } else {
            header('Location: /');
        }
        exit;
    }


    // Обработка GET-запроса для отображения проекта и его версий
    public function show(int $id_project) {
        $userId = $this->authenticate();

        $projectModel = $this->model('Project');
        $project = $projectModel->getProjectById($id_project);
        if (!$project || $project['created_by'] !== $userId) {
            http_response_code(403);
            die("Нет доступа к проекту.");
        }
        $versionModel = $this->model('VersionProject');
        $versions = $versionModel->getVersionsByProjectId($id_project);
        $this->view('project/view', [
            'project' => $project,
            'versions' => $versions
        ]);
    }
}
