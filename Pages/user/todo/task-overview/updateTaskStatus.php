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

if (isset($_POST["task_id"]) && isset($_POST["complete_status"]) && isset($_POST["completed_date"])) {
    $task_id = trim($_POST["task_id"]);
    $complete_status = trim($_POST["complete_status"]);
    $completed_date = $_POST["completed_date"];

    try {
        $stmt = $pdo->prepare("UPDATE task SET complete_status = :complete_status, completed_date = :completed_date WHERE userId = :user_id AND task_id = :task_id");
        $stmt->execute([":user_id" => $user_id, "complete_status" => $complete_status, "task_id" => $task_id, "completed_date" => $completed_date]);
        insertActivityLog($pdo, $user_id, "A013", "Task status updated to '$complete_status'");
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
}

?>