<?php
session_start();
require '../../../includes/db.php';
require '../../workspaces/workspace.php';

$userId = $_SESSION['user_id'];

$availableWorkspaces = getWorkspaces($pdo, $userId);
echo json_encode(['availableWorkspaces' => $availableWorkspaces]);
?>