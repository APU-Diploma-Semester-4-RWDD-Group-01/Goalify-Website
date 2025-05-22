<?php
include '../../includes/db.php';
include '../../includes/logging.php';
require_once '../workspaces/workspace.php';
session_start();
header("Content-Type: application/json");

$userId = $_SESSION["user_id"];

// fetch notification data
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    // fetch tasks that are going to due
    $stmtTask = $pdo->prepare('SELECT userId, task_name, complete_status, deadline, DATEDIFF(deadline, NOW()) AS daysLeft
                                    FROM task 
                                    WHERE userId = :userId
                                    AND complete_status = "doing"
                                    AND DATEDIFF(deadline, NOW()) <= 3');
    $stmtTask->execute([':userId' => $userId]);
    $dueTasks = $stmtTask->fetchAll(PDO::FETCH_ASSOC);

    // fetch project sub task that are going to due
    $stmtProject = $pdo->prepare('SELECT w.workspaceName, p.projectName, pst.projectSubTaskName, pst.assignedMemberId, 
                                pst.projectSubTaskStatus, DATEDIFF(pst.projectSubTaskDueDate, NOW()) AS daysLeft
                                FROM projectsubtask pst
                                JOIN projecttask pt ON pt.projectTaskId = pst.projectTaskId
                                JOIN project p ON pt.projectId = p.projectId 
                                JOIN workspace w ON p.workspaceId = w.workspaceId 
                                WHERE pst.assignedMemberId = :userId
                                AND projectSubTaskStatus = "in progress"
                                AND DATEDIFF(pst.projectSubTaskDueDate,NOW()) <= 3');
    $stmtProject->execute([':userId' => $userId]);
    $dueProjectSubTask = $stmtProject->fetchAll(PDO::FETCH_ASSOC);

    // fetch workspace invitation
    $stmtWorkspaceInvitation = $pdo->prepare('SELECT w.workspaceName, wi.workspaceId, wi.invitationId, wi.senderId, wi.receiverId, wi.sendDateTime, wi.invitationStatus, u.name, u.profile_img
                                            FROM workspaceinvitation wi
                                            JOIN user u ON u.userId = wi.senderId
                                            JOIN workspace w ON w.workspaceId = wi.workspaceId
                                            WHERE wi.receiverId = :userId
                                            AND wi.invitationStatus IS NULL');
    $stmtWorkspaceInvitation->execute([':userId' => $userId]);
    $workspaceInvitation = $stmtWorkspaceInvitation->fetchAll(PDO::FETCH_ASSOC);

    $result = [
        "task" => $dueTasks ?: null,
        "project" => $dueProjectSubTask ?: null,
        "invitation" => $workspaceInvitation ?: null,
    ];

    echo json_encode($result);
    exit;
}

// insert activity log
if (isset($_GET["insertActivityLog"])) {
    $userId = $_SESSION["user_id"];
    $actionId = $_POST["actionId"];
    $details = $_POST["details"];

    insertActivityLog($pdo, $userId, $actionId, $details);
}

// update the invitation status
if (isset($_GET["updateInvitationStatus"])) {
    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    $invitationId = $_POST["invitationId"];
    $invitationStatus = $_POST["invitationStatus"];

    try {
        $stmt = $pdo->prepare('UPDATE workspaceinvitation
                            SET invitationStatus = :status
                            WHERE invitationId = :id');
        $stmt->execute([':status' => $invitationStatus, ':id' => $invitationId]);
        if (getWorkspaceInvitationDetails($pdo, $invitationId, 'invitationStatus') == 'accept') {
            $workspaceId = getWorkspaceInvitationDetails($pdo, $invitationId, 'workspaceId');
            insertNewWorkspaceMember($pdo, $userId, $workspaceId);
        }
    } catch (PDOException $pdoE) {
        error_log("PDO error updating invitation status: " . $pdoE->getMessage());
    }
}


?>