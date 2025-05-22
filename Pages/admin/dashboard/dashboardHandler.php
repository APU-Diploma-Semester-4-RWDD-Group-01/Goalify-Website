<?php
include '../../includes/db.php';
// session_start();
header("Content-Type: application/json");

global $pdo;
if (!$pdo) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if (isset($_GET['workspace'])) {
    $filter = $_GET['workspace'];

    if ($filter == 'week') {
        $stmt = $pdo->prepare('SELECT DATE(createdDateTime) AS date,
        COUNT(*) AS totalWorkspace
        FROM workspace
        WHERE YEARWEEK(createdDateTime, 1) = YEARWEEK(CURDATE(), 1)
        GROUP BY DATE(createdDateTime)
        ORDER BY date ASC');

    } elseif ($filter == 'month') {
        $stmt = $pdo->prepare("SELECT DATE(DATE_SUB(createdDateTime, INTERVAL WEEKDAY(createdDateTime) DAY)) AS week,
        COUNT(*) AS totalWorkspace
        FROM workspace
        WHERE createdDateTime >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
        GROUP BY week
        ORDER BY week ASC");

    } elseif ($filter == 'year') {
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(createdDateTime, '%Y-%m') AS month,
        COUNT(*) AS totalWorkspace
        FROM workspace
        WHERE createdDateTime >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
        GROUP BY DATE_FORMAT(createdDateTime, '%M %Y')
        ORDER BY STR_TO_DATE(month, '%M %Y') ASC");
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$result) $result = []; 

    echo json_encode($result);
    exit();
}

if (isset($_GET['project'])) {
    $filter = $_GET['project'];

    if ($filter == 'week') {
        $stmt = $pdo->prepare('SELECT DATE(projectCreatedDateTime) AS date,
        COUNT(*) AS totalProject
        FROM project
        WHERE YEARWEEK(projectCreatedDateTime, 1) = YEARWEEK(CURDATE(), 1)
        GROUP BY DATE(projectCreatedDateTime)
        ORDER BY date ASC');

    } elseif ($filter == 'month') {
        $stmt = $pdo->prepare("SELECT DATE(DATE_SUB(projectCreatedDateTime, INTERVAL WEEKDAY(projectCreatedDateTime) DAY)) AS week,
        COUNT(*) AS totalProject
        FROM project
        WHERE projectCreatedDateTime >= DATE_FORMAT(CURDATE(), '%Y-%m-01')
        GROUP BY week
        ORDER BY week ASC");

    } elseif ($filter == 'year') {
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(projectCreatedDateTime, '%Y-%m') AS month,
        COUNT(*) AS totalProject
        FROM project
        WHERE projectCreatedDateTime >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
        GROUP BY DATE_FORMAT(projectCreatedDateTime, '%M %Y')
        ORDER BY STR_TO_DATE(month, '%M %Y') ASC");
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$result) $result = []; 

    echo json_encode($result);
    exit();
}

// for user metrics
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