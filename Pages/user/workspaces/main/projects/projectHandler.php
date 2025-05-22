<?php
session_start();
require '../../../../includes/db.php';
require '../../workspace.php';
require '../../../../includes/logging.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    if (isset($_POST['new-project-name']) && isset($_SESSION['workspace_id'])) {
        $newProjectName = $_POST['new-project-name'];
        $newProjectId = generateId('project');
        if (insertNewProject($pdo, $_SESSION['workspace_id'], $newProjectId, $newProjectName)) {
            insertActivityLog($pdo, $userId, 'A020', "New Project '$newProjectName' is created!");
            echo json_encode(['success' => "New Project '$newProjectName' is created!"]);
        } else {
            echo json_encode(['fail' => "Fail to create Project '$newProjectName' :("]);
        }
    }
    if (isset($_POST['action']) && isset($_POST['project-id'])) {
        if ($_POST['action'] == 'endProject') {
            $endProjectName = getProjectDetails($pdo, $_POST['project-id'], 'projectName');
            $datetime = new DateTime();
            $currentDateTime = $datetime->format('Y-m-d h:i:s');
            if (updateProject($pdo, 'projectEnd', $currentDateTime, $_POST['project-id']) && updateProject($pdo, 'projectStatus', 'completed', $_POST['project-id'])) {
                insertActivityLog($pdo, $userId, 'A025', "Project '$endProjectName' has ended!");
                echo json_encode(['success' => "Project '$endProjectName' has ended!"]);
            } else {
                echo json_encode(['fail' => "Fail to end Project '$endProjectName' :("]);
            }
        } else if ($_POST['action'] == 'deleteProject') {
            $deleteProjectName = getProjectDetails($pdo, $_POST['project-id'], 'projectName');
            if (deleteProject($pdo, $_POST['project-id'])) {
                insertActivityLog($pdo, $userId, 'A022', "Project '$deleteProjectName' is deleted!");
                echo json_encode(['success' => "Project '$deleteProjectName' is deleted!"]);
            } else {
                insertActivityLog($pdo, $userId, $actionId, "Fail to delete Project '$deleteProjectName' :(");
                echo json_encode(['fail' => "Fail to delete Project '$deleteProjectName' :("]);
            }
        } else if ($_POST['action'] == 'startProject') {
            $endProjectName = getProjectDetails($pdo, $_POST['project-id'], 'projectName');
            $datetime = new DateTime();
            $currentDateTime = $datetime->format('Y-m-d h:i:s');
            if (updateProject($pdo, 'projectStart', $currentDateTime, $_POST['project-id']) && updateProject($pdo, 'projectStatus', 'in progress', $_POST['project-id'])) {
                insertActivityLog($pdo, $userId, 'A021', "Project '$endProjectName' has started!");
                echo json_encode(['success' => "Project '$endProjectName' has started!"]);
            } else {
                echo json_encode(['fail' => "Fail to start Project '$endProjectName' :("]);
            }
        } else if ($_POST['action'] == 'openProject') {
            $_SESSION['project_id'] = $_POST['project-id'];
            $openProjectName = getProjectDetails($pdo, $_POST['project-id'], 'projectName');
            echo json_encode(['success' => "Project '$openProjectName' is opened!"]);
        }
    }
}
exit();
?>