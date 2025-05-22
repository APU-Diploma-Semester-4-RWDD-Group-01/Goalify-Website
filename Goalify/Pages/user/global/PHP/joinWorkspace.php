<?php
session_start();
require '../../../includes/db.php';
require '../../workspaces/workspace.php';
require '../../../includes/logging.php';

$userId = $_SESSION['user_id'];
error_log($userId);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['join-workspace-id'])) {
        $joinWorkspaceId = $_POST['join-workspace-id'];
        $result = insertNewWorkspaceMember($pdo, $userId, $joinWorkspaceId);
        $getWorkspaceName = getWorkspaceDetails($pdo, $joinWorkspaceId, 'workspaceName');
        if ($result === 'joined') {
            echo json_encode(['success' => "Previously joined Workspace '$getWorkspaceName' !"]);
        } else if ($result === true) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A019', "Successfully joined Workspace '$getWorkspaceName' !");
            echo json_encode(['success' => "Successfully joined Workspace '$getWorkspaceName' !"]);
        } else {
            echo json_encode(['fail' => "Failed to join Workspace '$getWorkspaceName' :("]);
        }
    }
}
exit();
?>