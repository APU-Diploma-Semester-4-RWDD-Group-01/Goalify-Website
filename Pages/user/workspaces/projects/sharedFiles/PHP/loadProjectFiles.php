<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';

if (isset($_SESSION['project_id'])) {
    $userId = $_SESSION['user_id'];
    $workspaceId = $_SESSION['workspace_id'];
    $selectedProjectId = $_SESSION['project_id'];
    $projectFiles = getAllProjectFiles($pdo, $selectedProjectId); // array of associative array
    $i = 0;
    foreach ($projectFiles as $index => $file) {
        unset($projectFiles[$index]['fileData']); // removes binary data, because it disrupt the json response
        $projectFiles[$index]['userName'] = getUserName($pdo, $file['userId']);
    }

    if ($projectFiles) {
        echo json_encode(['projectFiles' => $projectFiles]);
    } else {
        echo json_encode(['projectFiles' => null]);
    }
}
exit();
?>