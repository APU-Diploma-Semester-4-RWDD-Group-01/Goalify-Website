<?php
    ob_clean(); // Clean the output buffer
    header('Content-Type: application/json'); // response in json format
    session_start();

    require '../../../../includes/db.php';
    require '../../workspace.php';
    require '../../../../includes/logging.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['description']) && isset($_POST['projectId'])) {
            $description = $_POST['description'];
            $projectId = $_POST['projectId'];
        } elseif (isset($_POST['project-name']) && isset($_POST['projectId'])) {
            $projectName = $_POST['project-name'];
            $projectId = $_POST['projectId'];
        }
        if (isset($_POST['project-deadline'])) {
            $projectId = $_SESSION['project_id'];
            $projectDdl = $_POST['project-deadline'];
        }
        
    }

    if (isset($description) && isset($projectId)) {
        // update workspace description
        updateProject($pdo, 'projectDescription', $description, $projectId);

        // get description update timestamp
        $descriptionUpdate = getProjectDetails($pdo, $projectId, 'projectDescriptionUpdate');

        if ($descriptionUpdate) {
            $projectName = getProjectDetails($pdo, $projectId, 'projectName');
            insertActivityLog($pdo, $_SESSION['user_id'], 'A023', "Project '$projectName''s description is updated!");
            echo json_encode(['descriptionUpdate' => $descriptionUpdate]);
        } else {
            // Error: No update found, return error message
            echo json_encode(['error' => 'Failed to retrieve project description update']);
        }
    } elseif (isset($projectName) && isset($projectId)) {
        // update workspace name
        updateProject($pdo, 'projectName', $projectName, $projectId);

        // get updated workspace name
        $newProjectName = getProjectDetails($pdo, $projectId, 'projectName');
        
        if ($newProjectName) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A045', "Project Name is updated to '$newProjectName'!");
            echo json_encode(['projectName' => $newProjectName]);
        } else {
            // Error: No update found, return error message
            echo json_encode(['error' => 'Failed to retrieve project name']);
        }
    } else if (isset($projectDdl)) {
        updateProject($pdo, 'projectDeadline', $projectDdl, $projectId);
        $newProjectDeadline = getProjectDetails($pdo, $projectId, 'projectDeadline');
        if ($newProjectDeadline) {
            $projectName = getProjectDetails($pdo, $projectId, 'projectName');
            insertActivityLog($pdo, $_SESSION['user_id'], 'A024', "Project deadline for '$projectName' is set to '$newProjectDeadline'");
            echo json_encode(['success' => "Project deadline is set to '$newProjectDeadline'", "projectDdl" => $newProjectDeadline]);
        } else {
            echo json_encode(['fail' => "Project deadline is not updated"]);
        }
    } else {
        // Error: Missing workspace details
        echo json_encode(['error' => 'Missing project details']);
    }
    exit();
?>