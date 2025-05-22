<?php
include '../../../includes/db.php';

session_start();
$user_id = $_SESSION["user_id"];
if (!isset($_SESSION["user_id"])) {
    $path = "/Goalify/Pages/user/login & register/login.php";
    header("location: $path");
    exit();
}
require("../../../includes/session.php");
sessionTimeOut("/Goalify/Pages/user/login & register/login.php");

date_default_timezone_set("Asia/Kuala_Lumpur");
ob_clean();

// --------------------------------------------------------- fetch tasks from database -----------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header("Content-Type: application/json");

    $result = $pdo->prepare("SELECT task_id, task_name, deadline, complete_status FROM task WHERE userId = :user_id ORDER BY deadline ASC");
    $result->bindParam(":user_id", $user_id, PDO::PARAM_STR);
    $result->execute();
    $tasks = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($tasks);
    exit;
    
}

?>