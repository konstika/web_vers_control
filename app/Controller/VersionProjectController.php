<?php

namespace Controller;
use ZipArchive;
class VersionProjectController extends Controller
{

     //Отображение формы для создания новой версии
    public function new(int $id_project)
    {
        $userId = $this->authenticate();
        $projectModel = $this->model('Project');
        $project = $projectModel->getProjectById($id_project);
        if (!$project) {
            header("Location: /dashboard");
            exit;
        }
        $data = [
            'id_project' => $id_project,
            'project_name' => $project['name'],
            'message' => '',
            'description' => ''
        ];
        $this->view('project/version/new', $data);
    }

     //Обработка POST-запроса для создания новой версии
    public function create(int $id_project)
    {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project/{$id_project}");
            exit;
        }
        $projectModel = $this->model('Project');
        $project = $projectModel->getProjectById($id_project);
        if (!$project) {
            header("Location: /dashboard");
            exit;
        }
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if (empty($name)) {
            $error = "Название версии не может быть пустым.";
            $data = [
                'id_project' => $id_project,
                'project_name' => $project['name'],
                'name' => $name,
                'description' => $description,
                'error' => $error
            ];
            $this->view('project/version/new', $data);
            return;
        }

        $projectPath = $project['path'];
        $versionPath = $projectPath . uniqid();
        if (!mkdir($versionPath, 0777, true)) {
            $error = "Не удалось создать директорию для новой версии.";
            $this->view('project/version/new', compact('id_project', 'project', 'name', 'description', 'error'));
            return;
        }
        $versionData = [
            'id_project'  => $id_project,
            'name'        => $name,
            'path'        => $versionPath,
            'description' => $description,
        ];
        $versionModel = $this->model('VersionProject');
        if ($newVersionId = $versionModel->createVersion($userId, $versionData)) {

            // Создание записи истории
            $historyModel = $this->model('History');
            $historyModel->createHistoryRecord(
                $id_project,
                $newVersionId,
                null,
                "Создана новая версия: '{$name}'.",
                $userId
            );
            header("Location: /project/{$id_project}");
        } else {
            error_log("Failed to save version to DB. Deleting directory: " . $versionPath);
            rmdir($versionPath);
            $error = "Ошибка при сохранении версии в базе данных.";
            $data = [
                'id_project' => $id_project,
                'project_name' => $project['name'],
                'name' => $name,
                'description' => $description,
                'error' => $error
            ];
            $this->view('project/version/new', $data);
        }
        exit;
    }



    //Отображение страницы с деталями конкретной версии и файловым менеджером
    public function show(int $id_project, int $id_version)
    {
        $userId = $this->authenticate();
        $projectModel = $this->model('Project');
        $versionModel = $this->model('VersionProject');
        $version = $versionModel->getVersionById($id_version, $id_project);
        if (!$version) {
            header("Location: /project/{$id_project}");
            exit;
        }
        $project = $projectModel->getProjectById($id_project);
        $files = $this->getFilesInVersionDirectory($version['path']);

        $historyModel = $this->model('History');
        $versionHistory = $historyModel->getVersionHistory($id_project, $id_version);
        $data = [
            'project' => $project,
            'version' => $version,
            'files' => $files,
            'historyData' => $versionHistory,
            'isProjectView' => false,
        ];
        $this->view('project/version/view', $data);
    }


    //Отображение формы редактирования конкретной версии
    public function edit(int $id_project, int $id_version)
    {
        $this->authenticate();
        $versionModel = $this->model('VersionProject');
        $projectModel = $this->model('Project');
        $version = $versionModel->getVersionById($id_version, $id_project);
        $project = $projectModel->getProjectById($id_project);
        if (!$version || !$project) {
            header("Location: /project/{$id_project}");
            exit;
        }
        $data = [
            'version' => $version,
            'projectName' => $project['name'],
        ];
        $this->view('project/version/edit', $data);
    }


     //Обрабатка POST-запроса для обновления версии
    public function update(int $id_project, int $id_version)
    {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project/{$id_project}/version/{$id_version}/edit");
            exit;
        }
        $versionModel = $this->model('VersionProject');
        $projectModel = $this->model('Project');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $version = $versionModel->getVersionById($id_version, $id_project);
        $project = $projectModel->getProjectById($id_project);
        if (!$version || !$project) {
            header("Location: /project/{$id_project}");
            exit;
        }

        //Проверка данных
        if (empty($name)) {
            $error = "Название версии не может быть пустым.";
            $data = [
                'version' => $version,
                'projectName' => $project['name'],
                'error' => $error,
            ];
            $this->view('project/version/edit', $data);
            return;
        }

        // Обновление
        $updateData = [
            'name' => $name,
            'description' => $description,
        ];
        if ($versionModel->updateVersion($id_version, $updateData)) {
            // Запись в историю
            $changes = "";
            $oldName = $version['name'];
            $oldDescription = $version['description'] ?? '';
            $newName = $updateData['name'];
            $newDescription = $updateData['description'] ?? '';
            if ($oldName !== $newName) {
                $changes .= "Название изменено с '".$oldName." на '".$newName."'.";
            }
            if ($oldDescription !== $newDescription) {
                $changes .= "Описание было изменено.";
            }
            if ($changes != "") {
                $description = "Обновление версии '{$newName}': " . $changes;

            $historyModel = $this->model('History');
            $historyModel->createHistoryRecord(
                $id_project,
                $id_version,
                null,
                $description,
                $userId
            );
            }

            header("Location: /project/{$id_project}/version/{$id_version}"); // Успех: на страницу просмотра версии
        } else {
            $error = "Ошибка при сохранении изменений версии.";
            $data = [
                'version' => $version,
                'projectName' => $project['name'],
                'error' => $error,
            ];
            $this->view('project/version/edit', $data);
        }
        exit;
    }


    //Обработка POST-запроса для удаления версии
    public function delete(int $id_project, int $id_version)
    {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project/{$id_project}");
            exit;
        }
        $historyModel = $this->model('History');
        $versionModel = $this->model('VersionProject');
        $version = $versionModel->getVersionById($id_version, $id_project);
        $versionName = $version['name'];
        //Удаление зависимостей истории от версий для удаления
        if($historyModel->deleteVersionId($id_project,  $id_version, $versionName)) {
            $this->deleteVersionLogic($id_project, $id_version, $userId);
            // Создание записи истории
            $historyModel = $this->model('History');
            $historyModel->createHistoryRecord(
                $id_project,
                null,
                $versionName,
                "Удалена версия: '{$versionName}'.",
                $userId
            );
        }
        header("Location: /project/{$id_project}");
        exit;
    }

    //Удаление всех версий проекта
    public function deleteProjectVersions(int $id_project): bool
    {
        $versionModel = $this->model('VersionProject');
        $versions = $versionModel->getVersionsByProjectId($id_project);
        $userId = $this->authenticate();
        $success = true;
        foreach ($versions as $version) {
            $success = $success &&
                $this->deleteVersionLogic($id_project, $version['id_version_project'], $userId);
        }
        return $success;
    }

    private function deleteVersionLogic(int $id_project, int $id_version, int $userId): bool
    {
        $versionModel = $this->model('VersionProject');
        $version = $versionModel->getVersionById($id_version, $id_project);
        if (!$version) {
            return true;
        }
        $versionPath = $version['path'];
        $versionName = $version['name'];

        // Удаление записи из базы данных
        if ($versionModel->deleteVersion($id_version, $id_project)) {
            // Удаление директории
            if (is_dir($versionPath)) {
                $this->rmdir_recursive($versionPath);
            }
            return true;
        }
        return false; // Ошибка удаления из БД
    }


    //Вспомогательная функция для рекурсивного удаления непустой директории
    private function rmdir_recursive(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $items = array_diff(scandir($dir), array('.', '..'));
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->rmdir_recursive($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }


    //-----------------------------СОДЕРЖИМОЕ ВЕРСИИ--------------------------------------
    //Получение содержимого директории версии
    private function getFilesInVersionDirectory(string $versionPath): array
    {
        // Вспомогательная функция для форматирования размера файла в KB, MB, GB
        $formatSize = function ($bytes) {
            if ($bytes === 0) {
                return '0 B';
            }
            $sizes = ['B', 'KB', 'MB', 'GB'];
            $i = floor(log($bytes, 1024));
            return round($bytes / (1024 ** $i), 2) . ' ' . $sizes[$i];
        };
        $items = [];
        if (!is_dir($versionPath)) {
            error_log("Version directory not found: " . $versionPath);
            return $items;
        }
        $scan = scandir($versionPath);
        if ($scan === false) {
            error_log("Failed to scan directory: " . $versionPath);
            return $items;
        }
        foreach ($scan as $item) {
            if ($item === '.' || $item === '..') {continue;}// Пропускаем служебные ссылки
            $fullPath = rtrim($versionPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $item;
            $isDir = is_dir($fullPath);
            $size = '';
            if (!$isDir) {
                $bytes = filesize($fullPath);
                if ($bytes !== false) {$size = $formatSize($bytes);}
                else {$size = 'N/A';}// Не удалось получить размер
            } else {$size = 'Папка';}
            $items[] = [
                'name' => $item,
                'path' => $fullPath,
                'is_dir' => $isDir,
                'size' => $size,
            ];
        }
        return $items;
    }


    //Обработка POST-запроса для загрузки файлов
    public function upload(int $id_project, int $id_version)
    {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file']['name'][0])) {
            header("Location: /project/{$id_project}/version/{$id_version}");
            exit;
        }
        $versionModel = $this->model('VersionProject');
        $version = $versionModel->getVersionById($id_version, $id_project);
        if (!$version) {
            header("Location: /project/{$id_project}");
            exit;
        }
        $baseUploadPath = rtrim($version['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $filesArray = $this->rearrangeFilesArray($_FILES['file']);
        $uploadedCount = 0;
        $errorCount = 0;
        $firstItemName = '';
        $firstItemType = '';
        $failedFiles = [];
        foreach ($filesArray as $file) {
            // Пропускаем элемент, если загрузка была неудачной (например, слишком большой файл)
            if ($file['error'] !== UPLOAD_ERR_OK) {continue;}
            $relativePath = $file['full_path'] ?? basename($file['name']);
            $relativePath = trim(str_replace(['..', './'], '', $relativePath), '/');
            if (empty($relativePath)) continue; // Пропускаем пустой путь

            // Имя файла (без пути)
            $fileName = basename($relativePath);
            // Директория для размещения файла
            $targetDir = dirname($baseUploadPath . $relativePath);
            // Создание директории, если она не существует
            if (!is_dir($targetDir)) {
                $itemType = 'folder';
                if (!mkdir($targetDir, 0777, true)) {
                    error_log("Failed to create directory: " . $targetDir);
                    $errorCount++;
                    $failedFiles[] = htmlspecialchars($relativePath . " (Не удалось создать папку)");
                    continue;
                }
            }

            //Определение конечного пути и перемещение файла
            $destination = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $uploadedCount++;
            } else {
                $errorCount++;
                $failedFiles[] = htmlspecialchars($relativePath . " (Ошибка перемещения)");
            }
        }

        if ($uploadedCount > 0) {
            // Создание записи истории
            $historyModel = $this->model('History');
            $historyModel->createHistoryRecord(
                $id_project,
                $id_version,
                null,
                "Загружено {$uploadedCount} файлов/папок в версию: '{$version['name']}'.",
                $userId
            );
        }

        header("Location: /project/{$id_project}/version/{$id_version}");
        exit;
    }

    //Вспомогательный метод для преобразования $_FILES['file'] в массив отдельных файлов
    private function rearrangeFilesArray(array $file_post): array
    {
        $file_arr = [];
        $file_keys = array_keys($file_post);
        if (is_array($file_post['name'])) {
            $file_count = count($file_post['name']);
            for ($i = 0; $i < $file_count; $i++) {
                foreach ($file_keys as $key) {
                    $file_arr[$i][$key] = $file_post[$key][$i];
                }
            }
        } else {$file_arr[] = $file_post;}
        return $file_arr;
    }


    //Удаление файла/папки из версии
    public function deleteFile(int $id_project, int $id_version)
    {
        $userId = $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['item_path'])) {
            header("Location: /project/{$id_project}/version/{$id_version}");
            exit;
        }
        $itemPath = trim($_POST['item_path']);
        $versionModel = $this->model('VersionProject');
        $version = $versionModel->getVersionById($id_version, $id_project);
        if (!$version) {
            header("Location: /project/{$id_project}");
            exit;
        }
        $versionPath = rtrim($version['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (strpos($itemPath, $versionPath) !== 0) {
            header("Location: /project/{$id_project}/version/{$id_version}");
            exit;
        }

        $itemName = basename($itemPath);
        if (is_dir($itemPath)) {
            // Удаляем папку (рекурсивно)
            $this->rmdir_recursive($itemPath);
            $description = "Удалена папка: '{$itemName}'.";
        } elseif (is_file($itemPath)) {
            // Удаляем файл
            unlink($itemPath);
            $description = "Удален файл: '{$itemName}'.";
        }
        // Создание записи истории
        $historyModel = $this->model('History');
        $historyModel->createHistoryRecord(
            $id_project,
            $id_version,
            null,
            $description,
            $userId
        );

        header("Location: /project/{$id_project}/version/{$id_version}");
        exit;
    }


    // Обработка POST-запроса для скачивания всего содержимого версии архивом
    public function downloadAll(int $id_project, int $id_version)
    {
        $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project/{$id_project}/version/{$id_version}");
            exit;
        }
        $versionModel = $this->model('VersionProject');
        $version = $versionModel->getVersionById($id_version, $id_project);
        if (!$version) {
            header("Location: /project/{$id_project}");
            exit;
        }

        $versionPath = rtrim($version['path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $versionName = basename($version['name']);
        $zipFileName = tempnam(sys_get_temp_dir(), 'zip') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            // Ошибка при создании архива
            header("Location: /project/{$id_project}/version/{$id_version}");
            exit;
        }

        // Рекурсивное добавление файлов и папок
        $this->addFolderToZip($versionPath, $zip, $versionPath);
        $zip->close();

        // Отправка файла на скачивание
        $this->downloadFileResponse($zipFileName, "{$versionName}.zip");
    }

    // Обработка POST-запроса для скачивания отдельного файла или директории
    public function downloadFile(int $id_project, int $id_version)
    {
        $this->authenticate();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['item_path'])) {
            header("Location: /project/{$id_project}/version/{$id_version}");
            exit;
        }
        $itemPath = trim($_POST['item_path']);
        $versionModel = $this->model('VersionProject');
        $version = $versionModel->getVersionById($id_version, $id_project);
        if (!$version) {
            header("Location: /project/{$id_project}");
            exit;
        }
        $itemName = basename($itemPath);
        if (is_file($itemPath)) {
            // Скачивание файла
            $this->downloadFileResponse($itemPath, $itemName);
        } elseif (is_dir($itemPath)) {
            // Скачивание директории архивом
            $zipFileName = tempnam(sys_get_temp_dir(), 'zip') . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                header("Location: /project/{$id_project}/version/{$id_version}");
                exit;
            }
            // Имя директории внутри архива
            $archiveFolderName = basename($itemPath);
            // Рекурсивное добавление содержимого папки, сохраняя структуру
            $this->addFolderToZip($itemPath, $zip, dirname($itemPath) . DIRECTORY_SEPARATOR, $archiveFolderName);
            $zip->close();
            $this->downloadFileResponse($zipFileName, "{$itemName}.zip");
        } else {
            // Файл или директория не найдены
            header("Location: /project/{$id_project}/version/{$id_version}");
            exit;
        }
    }

    // Вспомогательная функция для рекурсивного добавления содержимого директории в ZipArchive
    private function addFolderToZip(string $folderPath, ZipArchive $zip, string $basePath, string $zipDir = '')
    {
        $files = array_diff(scandir($folderPath), array('.', '..'));
        $basePathLength = strlen($basePath);
        foreach ($files as $file) {
            $fullPath = $folderPath . DIRECTORY_SEPARATOR . $file;
            $relativePath = ($zipDir ? $zipDir . DIRECTORY_SEPARATOR : '') . substr($fullPath, $basePathLength);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath); // Использование '/' в zip
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $relativePath); // Добавление файла
            } elseif (is_dir($fullPath)) {
                $zip->addEmptyDir($relativePath); // Добавление пустой директории
                $this->addFolderToZip($fullPath, $zip, $basePath, $zipDir); // Рекурсивный вызов
            }
        }
    }

    // Вспомогательная функция для отправки файла на скачивание и удаления временного файла
    private function downloadFileResponse(string $filePath, string $downloadName)
    {
        // Очистка буфера вывода
        if (ob_get_level()) {
            ob_end_clean();
        }
        // Установка HTTP-заголовков для скачивания файла
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($downloadName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        // Считывание и вывод содержимого файла
        readfile($filePath);
        // Если файл был временным (например, ZIP), удаляем его
        if (strpos($filePath, sys_get_temp_dir()) !== false) {
            unlink($filePath);
        }
        exit;
    }


}