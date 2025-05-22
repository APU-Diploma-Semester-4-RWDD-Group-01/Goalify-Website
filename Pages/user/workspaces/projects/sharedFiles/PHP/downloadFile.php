<?php
session_start();
header('Content-Type: application/json');
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userHandler.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['downloadFileId'])) {
        downloadFile();
    }
}

function downloadFile() {
    global $pdo;
    $projectId = $_SESSION['project_id'];
    $fileId = $_GET['downloadFileId'];
    $fileName = getProjectFileDetails($pdo, $fileId, 'fileName');
    if ($fileName) {
        $fileType = getProjectFileDetails($pdo, $fileId, 'fileType');
        $fileData = getProjectFileDetails($pdo, $fileId, 'fileData');
        header("Content-Type: " . $fileType);
        header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
        header("Content-Length: " . strlen($fileData));
        echo $fileData;
    } else {
        echo "File '{$fileId}' is not found";
    }
}
exit();
?>