<?php
    // Author: Phang Shea Wen
    // Date: 2025/01/31
    // Filename: descriptionUpdate.php
    // Description: handle AJAX request sent by JS in 'description.php'
    //             (1) Update workspace description in db
    //             (2) Update workspace name in db
    session_start();
    ob_clean(); // Clean the output buffer
    header('Content-Type: application/json'); // response in json format

    require '../../../../includes/db.php';
    require '../../workspace.php';
    require '../../../../includes/logging.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['description']) && isset($_POST['workspaceId'])) {
            $description = $_POST['description'];
            $workspaceId = $_POST['workspaceId'];
        } elseif (isset($_POST['workspace-name']) && isset($_POST['workspaceId'])) {
            $workspaceName = $_POST['workspace-name'];
            $workspaceId = $_POST['workspaceId'];
        }
        
    }

    if (isset($description) && isset($workspaceId)) {
        // update workspace description
        updateWorkspace($pdo, 'workspaceDescription', $description, $workspaceId);

        // get description update timestamp
        $descriptionUpdate = getWorkspaceDetails($pdo, $workspaceId, 'descriptionUpdate');

        $workspaceName = getWorkspaceDetails($pdo, $workspaceId, 'workspaceName');
        if ($descriptionUpdate) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A015', "Workspace '$workspaceName''s description is updated!");
            echo json_encode(['descriptionUpdate' => $descriptionUpdate]);
        } else {
            // Error: No update found, return error message
            echo json_encode(['error' => 'Failed to retrieve workspace description update']);
        }
    } elseif (isset($workspaceName) && isset($workspaceId)) {
        // update workspace name
        updateWorkspace($pdo, 'workspaceName', $workspaceName, $workspaceId);

        // get updated workspace name
        $newWorkspaceName = getWorkspaceDetails($pdo, $workspaceId, 'workspaceName');
        
        if ($newWorkspaceName) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A040', "Workspace Name is updated to '$newWorkspaceName'!");
            echo json_encode(['workspaceName' => $newWorkspaceName]);
        } else {
            // Error: No update found, return error message
            echo json_encode(['error' => 'Failed to retrieve workspace name']);
        }
    } else {
        // Error: Missing workspace details
        echo json_encode(['error' => 'Missing workspace details']);
    }
    exit();
?>