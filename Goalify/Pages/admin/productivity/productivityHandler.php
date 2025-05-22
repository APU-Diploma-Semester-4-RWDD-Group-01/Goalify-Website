<?php
include '../../includes/db.php';
// session_start();
header("Content-Type: application/json");

// search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search-users-input'])) {
    global $pdo;
    $keyword = trim($_POST['search-users-input']);
    $searchUsers = searchUserByKeyWord($pdo, $keyword);
    echo json_encode($searchUsers);
    exit;
}

// get registsred user list
if (isset($_GET['getUser'])) {

    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT userId, name, profile_img 
                                FROM user');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
    exit;
}

// get workspace data
if (isset($_GET['getWorkspaceOverview'])) {

    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT 
                                COUNT(*) AS totalWorkspace,
                                COUNT(CASE WHEN DATE_FORMAT(createdDateTime, "%Y-%m") = DATE_FORMAT(CURDATE(), "%Y-%m") THEN 1 END) AS currentMonth,
                                COUNT(CASE WHEN DATE_FORMAT(createdDateTime, "%Y-%m") = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), "%Y-%m") THEN 1 END) AS lastMonth
                                FROM workspace');
    $stmt->execute();
    $workspace = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($workspace);
    exit;
}

// get project data
if (isset($_GET['getProjectOverview'])) {

    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    $stmt = $pdo->prepare('SELECT 
                                COUNT(*) AS totalProject,
                                COUNT(CASE WHEN DATE_FORMAT(ProjectCreatedDateTime, "%Y-%m") = DATE_FORMAT(CURDATE(), "%Y-%m") THEN 1 END) AS currentMonth,
                                COUNT(CASE WHEN DATE_FORMAT(ProjectCreatedDateTime, "%Y-%m") = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), "%Y-%m") THEN 1 END) AS lastMonth
                                FROM project');
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($project);
    exit;
}

// get user details for selected user
if (isset($_GET['getUserDetails'])) {

    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    if (!isset($_GET['userId'])) {
        echo "User ID not provided.";
        exit;
    }

    $userId = $_GET['userId'];

    $stmtUser = $pdo->prepare('SELECT userId, name, profile_img, email 
                                FROM user
                                WHERE userId = :userId');
    $stmtUser->execute([':userId' => $userId]);
    $user = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

    $stmtWorkspace =  $pdo->prepare('SELECT w.workspaceId, w.workspaceName, "Owner" AS role, w.createdDateTime AS dateTime
                                        FROM workspace w
                                        WHERE w.ownerId = :userId
                                        
                                        UNION
                                        
                                        SELECT wm.workspaceId, w.workspaceName, "Member" AS role, wm.joinedDateTime AS dateTime
                                        FROM workspacemember wm
                                        JOIN workspace w 
                                        ON wm.workspaceId = w.workspaceId
                                        WHERE wm.memberId = :userId');
    $stmtWorkspace->execute([':userId' => $userId]);
    $workspace = $stmtWorkspace->fetchAll(PDO::FETCH_ASSOC);

    $stmtProject =  $pdo->prepare('SELECT DISTINCT w.workspaceName, p.projectName, p.projectStart, p.projectStatus, p.projectDeadline
                                        FROM project p
                                        JOIN workspace w ON p.workspaceId = w.workspaceId
                                        WHERE p.projectId IN (
                                            SELECT pt.projectId
                                            FROM projectTask pt
                                            JOIN projectSubTask pst ON pt.projectTaskId = pst.projectTaskId
                                            WHERE pst.assignedMemberId = :userId
                                        )');
    $stmtProject->execute([':userId' => $userId]);
    $project = $stmtProject->fetchAll(PDO::FETCH_ASSOC);

    $stmtTask =  $pdo->prepare('SELECT task_name, category, created_at, completed_date, complete_status
                                    FROM task
                                    WHERE userId = :userId');
    $stmtTask->execute([':userId' => $userId]);
    $task = $stmtTask->fetchAll(PDO::FETCH_ASSOC);
                                        
    if (!$user) {
        echo "User not found.";
        exit;
    }

    $result = [
        "user"=> $user ?: null,
        "workspace"=> $workspace ?: null,
        "project"=> $project ?: null,
        "task"=> $task ?: null
    ];

    echo json_encode($result);
    exit;
}

// search user
function searchUserByKeyWord($pdo, $keyword) {
    try {
        $stmt = $pdo->prepare("SELECT userId, name, profile_img FROM `user` WHERE `name` LIKE :keyword");
        $wildcard = "%{$keyword}%";
        $stmt->execute([':keyword' => $wildcard]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $pdoE) {
        error_log("PDO error getting searched users: " . $pdoE->getMessage());
        return ["error" => "Database error occurred"];
    }
}
?>