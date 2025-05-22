<?php
include '../../includes/db.php';
header("Content-Type: application/json");

// user details
if (isset($_GET['getUser'])) {
    try {
        $stmt = $pdo->prepare('SELECT userId AS id, name, profile_img FROM user');
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
    exit;
}

// user details
if (isset($_GET['getUserDetails']) && isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    try {
        // fetch user details
        $stmt = $pdo->prepare('SELECT * FROM user WHERE userId = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            echo json_encode(['error' => 'User not found']);
            exit;
        }
        // fetch last login
        $lastLoginStmt = $pdo->prepare('SELECT timestamp FROM activitylog WHERE userId = ? AND actionId = "A001" ORDER BY timestamp DESC LIMIT 1');
        $lastLoginStmt->execute([$userId]);
        $lastLogin = $lastLoginStmt->fetchColumn();
        // fetch tasks completed
        $tasksCompletedStmt = $pdo->prepare('SELECT COUNT(*) FROM task WHERE userId = ? AND complete_status = "done"');
        $tasksCompletedStmt->execute([$userId]);
        $tasksCompleted = $tasksCompletedStmt->fetchColumn();
        // fetch workspaces involved
        $workspacesInvolvedStmt = $pdo->prepare('
            SELECT COUNT(DISTINCT w.workspaceId)
            FROM (
                SELECT workspaceId FROM workspace WHERE ownerId = ?
                UNION
                SELECT workspaceId FROM workspacemember WHERE memberId = ?
            ) AS w
        ');
        $workspacesInvolvedStmt->execute([$userId, $userId]);
        $workspacesInvolved = $workspacesInvolvedStmt->fetchColumn();
        // fetch projects involved
        $projectsInvolvedStmt = $pdo->prepare('
            SELECT COUNT(DISTINCT p.projectId)
            FROM project p
            JOIN projectTask pt ON p.projectId = pt.projectId
            JOIN projectSubTask pst ON pt.projectTaskId = pst.projectTaskId
            WHERE pst.assignedMemberId = ?
        ');
        $projectsInvolvedStmt->execute([$userId]);
        $projectsInvolved = $projectsInvolvedStmt->fetchColumn();
        // fetch projects completed
        $projectsCompletedStmt = $pdo->prepare('
            SELECT COUNT(DISTINCT p.projectId)
            FROM project p
            JOIN projectTask pt ON p.projectId = pt.projectId
            JOIN projectSubTask pst ON pt.projectTaskId = pst.projectTaskId
            WHERE pst.assignedMemberId = ? AND p.projectStatus = "completed"
        ');
        $projectsCompletedStmt->execute([$userId]);
        $projectsCompleted = $projectsCompletedStmt->fetchColumn();
        $result = [
            "user" => [$user],
            "activity" => [
                "lastLogin" => $lastLogin,
                "tasksCompleted" => $tasksCompleted,
                "workspacesInvolved" => $workspacesInvolved,
                "projectsInvolved" => $projectsInvolved,
                "projectsCompleted" => $projectsCompleted,
            ],
        ];
        echo json_encode($result);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['error' => 'Database error']);
    }
    exit;
}

function getRegisteredUsers($pdo) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM `user`');
        $stmt->execute();
        $users = $stmt->fetchAll();
        return $users;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting registered users: " . $pdoE->getMessage());
        return null;
    }
}

if (isset($_GET['allUsers'])) {
    echo json_encode(getRegisteredUsers($pdo, 100));
    exit;
}

// search
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search-users-input'])) {
    $keyword = trim($_POST['search-users-input']);
    $searchUsers = searchUserByKeyWord($pdo, $keyword);
    echo json_encode($searchUsers);
    exit;
}

function searchUserByKeyWord($pdo, $keyword) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM `user` WHERE `name` LIKE :keyword");
        $wildcard = "%{$keyword}%";
        $stmt->execute([':keyword' => $wildcard]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting searched users: " . $pdoE->getMessage());
        return ["error" => "Database error occurred"];
    }
}

// user activity
if (isset($_GET['getUserActivity']) && isset($_GET['id'])) {
    include_once '../../includes/db.php';
    global $pdo;
    $userId = $_GET['id'];
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }
    try {
        $queryUser = "SELECT userId, name, DATE(joinedDateTime) AS joinedDate FROM user WHERE userId = :userId";
        $stmtUser = $pdo->prepare($queryUser);
        $stmtUser->execute([':userId' => $userId]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            echo json_encode(["error" => "No user found for userId: $userId"]);
            exit;
        }
        $queryActivity = 
            "SELECT timestamp, actionType, details, ipAddress, deviceBrowser 
            FROM activitylog as al
            JOIN action as a
            ON al.actionId = a.actionId
            WHERE userId = :userId 
            ORDER BY timestamp DESC";
        $stmtActivity = $pdo->prepare($queryActivity);
        $stmtActivity->execute([':userId' => $userId]);
        $activity = $stmtActivity->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "user" => $user,
            "activity" => $activity ?: []
        ]);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database query failed: " . $e->getMessage()]);
    }
    exit;
}

// contact
if (isset($_GET['getUserEmail']) && isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    $stmt = $pdo->prepare('SELECT email FROM user WHERE userId = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo json_encode(["email" => $user['email']]);
    } else {
        echo json_encode(["error" => "User email not found"]);
    }
    exit;
}


if (isset($_GET['getMetrics'])) {
    $response = [];
    // total users
    $result = $pdo->query("SELECT COUNT(*) AS total FROM user");
    $response['totalUsers'] = $result->fetchColumn();
    // sign-ups (monthly)
    $signupQuery = $pdo->query("SELECT MONTHNAME(joinedDateTime) AS month, COUNT(*) AS count FROM user GROUP BY MONTH(joinedDateTime) ORDER BY joinedDateTime ASC");
    $months = [];
    $signups = [];
    while ($row = $signupQuery->fetch(PDO::FETCH_ASSOC)) {
        $months[] = $row['month'];
        $signups[] = (int) $row['count'];
    }
    $response['months'] = $months;
    $response['signups'] = $signups;
    // active users (weekly)
    $activeUsersQuery = $pdo->query("SELECT 
        DATE_FORMAT(DATE_SUB(timestamp, INTERVAL WEEKDAY(timestamp) DAY), '%m-%d') AS week_start,
        COUNT(DISTINCT userId) AS count 
        FROM activitylog 
        WHERE actionId = 'A001' 
        GROUP BY YEAR(timestamp), WEEK(timestamp) 
        ORDER BY MIN(timestamp) ASC
    ");
    $weeks = [];
    $activeUsers = [];
    while ($row = $activeUsersQuery->fetch(PDO::FETCH_ASSOC)) {
        $weeks[] = $row['week_start'] ?? 'No Data';
        $activeUsers[] = (int) ($row['count'] ?? 0);
    }
    $response['weeks'] = $weeks;
    $response['activeUsers'] = $activeUsers;    
    echo json_encode($response);
}


?>