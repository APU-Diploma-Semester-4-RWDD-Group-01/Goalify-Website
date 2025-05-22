<?php
session_start();
$user_id = $_SESSION["user_id"];
if (!isset($_SESSION["user_id"])) {
    $path = "/Goalify/Pages/user/login & register/login.php";
    header("location: $path");
    exit();
}
require("../../../includes/session.php");
sessionTimeOut("/Goalify/Pages/user/login & register/login.php");

include '../../../includes/db.php';
include '../../../includes/logging.php';

header("Content-Type: application/json");
ob_clean();

function generateTaskId() {
    return "#TASK" . strtoupper(substr(md5(uniqid()), 0, 5));
}

// --------------------------------------------------------- save data to database -----------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: text/plain');
    if (isset($_POST["task-name"])) {
        $task_name = trim($_POST["task-name"]);
        $task_description = trim($_POST["task-description"] ?? "");
        $task_category = trim($_POST["category"] ?? "");
        $task_complete_status = trim($_POST["complete_status"] ?? "");
        $user_id = $_SESSION["user_id"] ?? "";
        $task_id = generateTaskId();
        error_log($task_id);
        ob_flush();
        flush();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO task (task_id, task_name, task_description, category, complete_status, userId) VALUES (:task_id, :task_name, :task_description, :category, :complete_status, :user_id)");
        $stmt->bindParam(":task_id", $task_id);
        $stmt->bindParam(":task_name", $task_name);
        $stmt->bindParam(":task_description", $task_description);
        $stmt->bindParam(":category", $task_category);
        $stmt->bindParam(":complete_status", $task_complete_status);
        $stmt->bindParam(":user_id", $user_id);

        if ($stmt->execute()) {
            echo "Task saved successfully!";
            insertActivityLog($pdo, $user_id, "A039", "Added task '$task_name'");
            exit;
        } else {
            echo "Failed to save task.";
            exit;
        }
    }catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }  
    
    
}


// --------------------------------------------------------- fetch tasks from database -----------------------------------------------------------

$current_date = date("Y-m-d");

$updateStmt = $pdo->prepare("UPDATE task SET complete_status = 'past_date' WHERE userId = :user_id 
                            AND deadline IS NOT NULL
                            AND (deadline < :current_date OR deadline < created_at)
                            AND TRIM(complete_status) != 'done'");
$updateStmt->execute([
    ":user_id" => $user_id,
    "current_date" => $current_date
]);

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $result = $pdo->prepare("SELECT * FROM task WHERE userId = :user_id AND (deadline >= :current_date OR deadline IS NULL) ORDER BY created_at ASC");
    $result->execute([
        ":user_id" => $user_id,
        "current_date" => $current_date
    ]);

    $row = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($row);    
}

?>