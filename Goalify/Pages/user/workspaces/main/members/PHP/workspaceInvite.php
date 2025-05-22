<?php
session_start();
header('Content-Type: application/json');
require_once '../../../../../includes/db.php';
require_once '../../../workspace.php';
require_once '../../../../../includes/userFunction.php';
require '../../../../../includes/logging.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add-member-id'])) {
        $workspaceId = $_SESSION['workspace_id'];
        $senderId = $_SESSION['user_id'];
        $receiverId = $_POST['add-member-id'];
        if (getUserById($pdo, $receiverId)) {
            if (insertWorkspaceInvitation($pdo, $workspaceId, $senderId, $receiverId)) {
                // A040 or A016
                insertActivityLog($pdo, $senderId, 'A016', "Workspace Invitation is sent to User '$receiverId'!");
                echo json_encode(['success' => "Workspace Invitation is sent to User '$receiverId'!"]);
            } else {
                echo json_encode(['fail' => "Fail to Send Workspace Invitation to User '$receiverId'"]);
            }
        } else {
            echo json_encode(['fail' => "User '$receiverId' is not found"]);
        }
    }
    exit();
}
?>