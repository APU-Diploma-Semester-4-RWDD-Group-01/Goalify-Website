<?php
session_start();
require '../../../../includes/db.php';
require '../../workspace.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['workspace-id'])) {
    $workspaceId = $_POST['workspace-id'];
    $_SESSION['workspace_id'] = $workspaceId;
    echo json_encode(['success' => 'Workspace ID stored in session']);
} else {
    echo json_encode(['fail' => 'Failed to retrieve all projects']);
}
exit();
?>
