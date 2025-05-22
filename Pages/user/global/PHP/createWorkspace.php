<?php
session_start();
require '../../../includes/db.php';
require '../../workspaces/workspace.php';
require '../../../includes/logging.php';

$userId = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['new-workspace-name'])) {
        $newWorkspaceName = $_POST['new-workspace-name'];
        $newWorkspaceId = generateId('workspace');
        if (insertNewWorkspace($pdo, $userId, $newWorkspaceId, $newWorkspaceName)) {
            $getWorkspaceName = getWorkspaceDetails($pdo, $newWorkspaceId, 'workspaceName');
            insertActivityLog($pdo, $_SESSION['user_id'], 'A014', "Workspace '$getWorkspaceName' is created !");
            echo json_encode(['success' => "Workspace '$getWorkspaceName' is created !"]);
        } else {
            echo json_encode(['fail' => "Workspace '$getWorkspaceName' is not created :("]);
        }
    }
}
exit();
?>