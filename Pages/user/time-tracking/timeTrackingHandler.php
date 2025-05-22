<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}

require('../../includes/db.php');
require('../../includes/session.php');
require('../../includes/logging.php');
header('Content-Type: application/json');
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');

// generate time tracking id
function generateTimeTrackingId()
{
    return "#TID" . strtoupper(substr(md5(uniqid()), 0, 5));
}

// insert focus record
if (isset($_GET['insertFocusRecord'])) {
    $timeTrackingId = generateTimeTrackingId();
    $userId = $_SESSION["user_id"];
    $taskId = $_POST["taskId"];
    $startTime = $_POST["startTime"];
    $endTime = $_POST["endTime"];
    $duration = $_POST["duration"];
    $timeTrackingDate = $_POST["timeTrackingDate"];

    try {
        $stmt = $pdo->prepare('INSERT INTO `focusrecord`(`timeTrackingId`, `userId`, `taskId`, `startTime` , `endTime`, `duration`, `timeTrackingDate`) VALUES
                                    (:timeTrackingId, :userId, :taskId, :startTime, :endTime, :duration, :timeTrackingDate);');
        $stmt->execute([':timeTrackingId' => $timeTrackingId, ':userId' => $userId, ':taskId' => $taskId, ':startTime' => $startTime, ':endTime' => $endTime, ':duration' => $duration, ':timeTrackingDate' => $timeTrackingDate]);
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new focus record: " . $pdoE->getMessage());
    }
}

// insert activity log
if (isset($_GET['insertActivityLog'])) {
    $userId = $_SESSION["user_id"];
    $actionId = $_POST["actionId"];
    $details = $_POST["details"];

    insertActivityLog($pdo, $userId, $actionId, $details);
}

// fetch task from database
if (isset($_GET['getTasks'])) {

    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    $userId = $_SESSION["user_id"];

    $stmt = $pdo->prepare('SELECT userId, task_id, task_name, complete_status 
                                FROM task 
                                WHERE userId = :userId
                                AND complete_status = "doing"');
    $stmt->execute([':userId' => $userId]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($tasks);
    exit;
}

// format the unit of duration to hr, min, sec
function formatDuration($second)
{
    $hr = floor($second / 3600);
    $min = floor(($second % 3600) / 60);
    $sec = $second % 60;

    $result = [];
    if ($hr > 0) {
        $result[] = "$hr hr";
    }
    if ($min > 0) {
        $result[] = "$min min";
    }
    if ($sec > 0) {
        $result[] = "$sec sec";
    }

    return implode(" ", $result);
}


// get focus records from database
if (isset($_GET['getRecords'])) {

    global $pdo;
    if (!$pdo) {
        echo json_encode(["error" => "Database connection failed"]);
        exit;
    }

    $userId = $_SESSION["user_id"];

    $stmt = $pdo->prepare('SELECT taskId, task_name, startTime, endTime, duration
                                FROM focusrecord 
                                LEFT JOIN task 
                                ON task.task_id = focusrecord.taskId
                                WHERE focusrecord.userId = :userId 
                                AND timeTrackingDate >= CURRENT_DATE
                                AND timeTrackingDate < CURRENT_DATE + INTERVAL 1 DAY
                                ORDER BY duration DESC');
    $stmt->execute([':userId' => $userId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($records as &$record) {
        $record['startTime'] = date("H:i:s", strtotime($record['startTime']));
        $record['endTime'] = date("H:i:s", strtotime($record['endTime']));
        $record['duration'] = formatDuration($record['duration']);
    }
    unset($record);

    echo json_encode($records);
    exit;
}
?>