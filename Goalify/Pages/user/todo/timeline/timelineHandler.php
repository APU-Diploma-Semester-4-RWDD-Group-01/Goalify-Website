<?php
include '../../../includes/db.php';
include '../../../includes/logging.php';

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
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_clean();

function generateTimeId() {
    return "#TIME" . strtoupper(substr(md5(uniqid()), 0, 5));
}

// --------------------------------------------------------- fetch tasks from database -----------------------------------------------------------

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header("Content-Type: application/json");

    try {
        if (isset($_GET["date"])){
            $date = isset($_GET["date"]) ? $_GET["date"] : date("Y-m-d");
    

            $stmt = $pdo->prepare("SELECT timeline.time_plan, task.task_name
                                    FROM timeline 
                                    LEFT JOIN task ON timeline.task_id = task.task_id
                                    WHERE timeline.plan_date = :date AND task.userId = :user_id
                                    ORDER BY timeline.time_plan ASC");


            $stmt->bindParam(":date", $date, PDO::PARAM_STR);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
            $stmt->execute();
        
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            echo json_encode($tasks);
            exit;
        } 


        if (isset($_GET['unscheduled']) && $_GET['unscheduled'] === "true") {
            $stmt = $pdo->prepare("SELECT task_id, task_name FROM task WHERE complete_status = 'doing' AND userId = :user_id ORDER BY created_at ASC");

            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_STR);
            $stmt->execute();

            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tasks);
            exit;
        }
        
    } catch (Exception $e) {
        echo json_encode(["error" => "Error fetching tasks: " . $e->getMessage()]);
    }

    
    if (isset ($_GET["task_name"])) {
        $task_name = $_GET["task_name"];
        $task_desc = $pdo->prepare("SELECT task_name, task_description FROM task WHERE task_name = :task_name AND userId = :user_id LIMIT 1");

    
        $task_desc->bindParam(":task_name", $task_name, PDO::PARAM_STR);
        $task_desc->bindParam(":user_id", $user_id, PDO::PARAM_STR);

        $task_desc->execute();
        $result = $task_desc->fetch(PDO::FETCH_ASSOC);
    
        if (empty($result)) {
            echo json_encode(["message" => "No tasks found"]);
            // exit;
        }else{
        echo json_encode($result);
        }
        exit;
    }    
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json");

    if (isset($_POST["task_id"], $_POST["task_name"], $_POST["time_plan"], $_POST["deadline"], $_POST["plan_date"])) {
        $task_id = trim($_POST["task_id"]);
        $task_name = trim($_POST["task_name"]);
        $task_time_plan = trim($_POST["time_plan"]);
        $task_deadline = trim($_POST["deadline"]);
        $task_plan_date = date("Y-m-d", strtotime($_POST["plan_date"] ?? date("Y-m-d")));
        $user_id = $_SESSION["user_id"] ?? "";
        $schedule_id = generateTimeId();
    } else {
        echo json_encode(["error" => "All fields are required."]);
        exit;
    }

    try {
        $stmt1 = $pdo->prepare("INSERT INTO timeline (schedule_id, task_id, time_plan, plan_date) VALUES (:schedule_id, :task_id, :time_plan, :plan_date)");       

        $stmt2 = $pdo->prepare("UPDATE task 
                                SET deadline = :deadline 
                                WHERE task_id = :task_id AND userId = :user_id");


        $stmt1->bindParam(":schedule_id", $schedule_id, PDO::PARAM_STR);
        $stmt1->bindParam(":task_id", $task_id, PDO::PARAM_STR);
        $stmt1->bindParam(":time_plan", $task_time_plan);
        $stmt1->bindParam(":plan_date", $task_plan_date);
        
        $stmt2->bindParam(":deadline", $task_deadline);
        $stmt2->bindParam(":task_id", $task_id, PDO::PARAM_STR);
        $stmt2->bindParam(":user_id", $user_id, PDO::PARAM_STR);

        if ($stmt1->execute() && $stmt2->execute()) {
            insertActivityLog($pdo, $user_id, "A038", "Updated deadline for '$task_name'");
            insertActivityLog($pdo, $user_id, "A047", "Scheduled time for '$task_name'");
            insertActivityLog($pdo, $user_id, "A012", "Task '$task_name' added to timeline");
            echo json_encode(["message" => "Task updated successfully!"]);
            exit;
        } else {
            echo json_encode(["error" => "Failed to update task."]);
            
        }
        exit;
    } catch (Exception $e) {
        echo json_encode(["error" => "Error: " . $e->getMessage()]);
    }
}

?>