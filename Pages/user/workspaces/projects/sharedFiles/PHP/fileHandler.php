<?php
session_start();
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';
require '../../../../../includes/logging.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['new-file'])) {
        uploadFile();
    }
    if (isset($_POST['deleteFileId'])) {
        deleteFile();
    }
}

function uploadFile() {
    global $pdo;
    $projectId = $_SESSION['project_id'];
    $fileName = $_FILES['new-file']['name'];
    $fileType = $_FILES['new-file']['type'];
    $fileSize = $_FILES['new-file']['size'];
    $userId = $_SESSION['user_id'];
    $fileData = file_get_contents($_FILES['new-file']['tmp_name']); // returns raw binary data
    if ($fileSize > 0) {
        $projectName = getProjectDetails($pdo, $projectId, 'projectName');
        if(insertNewProjectFile($pdo, $userId, $projectId, $fileName, $fileType, $fileSize, $fileData)) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A035', "File {$fileName} is uploaded to Project '$projectName'!");
            echo json_encode(['success' => "File {$fileName} is uploaded"]);
        } else {
            echo json_encode(['fail' => "File {$fileName} is not uploaded"]);
        }
    } else {
        echo json_encode(['fail' => "File {$fileName} is not uploaded"]);
    }
}

function deleteFile() {
    global $pdo;
    $projectId = $_SESSION['project_id'];
    $fileId = $_POST['deleteFileId'];
    $fileName = getProjectFileDetails($pdo, $fileId, 'fileName');
    if (deleteProjectFile($pdo, $fileId, $projectId)) {
        $projectName = getProjectDetails($pdo, $projectId, 'projectName');
        insertActivityLog($pdo, $_SESSION['user_id'], 'A037', "File {$fileName} is deleted from Project '$projectName'!");
        echo json_encode(['success' => "File '{$fileName}' is deleted"]);
    } else {
        echo json_encode(['fail' => "File '{$fileName}' is not deleted"]);
    }
}
exit();
?>