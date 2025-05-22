<?php
session_start();
header('Content-Type: application/json');
require '../../../includes/db.php';
require '../../../includes/logging.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    if (isset($_POST['new-short-goal'])) {
        $goalName = $_POST['new-short-goal'];
        if (insertNewGoal($pdo, $goalName, 'short-term', $userId)) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A007', "New Short-Term Goal '{$goalName}' is added!");
            echo json_encode(['success' => "New Short-Term Goal '{$goalName}' is added!"]);
        } else {
            echo json_encode(['fail' => "Goal '{$goalName}' is not added"]);
        }
    }
    if (isset($_POST['new-long-goal'])) {
        $goalName = $_POST['new-long-goal'];
        if (insertNewGoal($pdo, $goalName, 'long-term', $userId)) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A008', "New Long-Term Goal '{$goalName}' is added!");
            echo json_encode(['success' => "New Long-Term Goal '{$goalName}' is added!"]);
        } else {
            echo json_encode(['fail' => "Goal '{$goalName}' is not added"]);
        }
    }
    if (isset($_POST['delete-goal-id'])) {
        $goalId = $_POST['delete-goal-id'];
        $goalName = $_POST['delete-goal-name'];
        if (deleteGoal($pdo, $goalId)) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A046', "Goal '{$goalName}' is deleted");
            echo json_encode(['success' => "Goal '{$goalName}' is deleted"]);
        } else {
            echo json_encode(['fail' => "Goal '{$goalName}' is not deleted"]);
        }
    }
    if (isset($_POST['completed-goal-id'])) {
        $goalId = $_POST['completed-goal-id'];
        $goalName = $_POST['completed-goal-name'];
        if (completeGoal($pdo, $goalId)) {
            insertActivityLog($pdo, $_SESSION['user_id'], 'A009', "Goal '{$goalName}' is completed");
            echo json_encode(['success' => "Goal '{$goalName}' is completed"]);
        } else {
            echo json_encode(['fail' => "Goal '{$goalName}' is not completed"]);
        }
    }
}

function insertNewGoal($pdo, $newGoal, $goalType, $userId) {
    try {
        $goalId = generateGoalId();
        $stmt = $pdo -> prepare('INSERT INTO `goal`(`goalId`, `goalName`, `goalType`, `completionStatus`, `userId`) VALUES
                                (:goalId, :goalName, :goalType, :completionStatus, :userId);');
        $stmt -> execute([':goalId' => $goalId, ':goalName' => $newGoal, ':goalType' => $goalType, ':completionStatus' => 'incomplete', ':userId' => $userId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error inserting new goal: ".$pdoE -> getMessage());
        return false;
    }
}

function deleteGoal($pdo, $goalId) {
    try {
        $stmt = $pdo -> prepare('DELETE FROM `goal` WHERE `goalId` = :goalId;');
        $stmt -> execute([':goalId' => $goalId]);
        return true;
    } catch (Exception $e) {
        error_log("Error deleting goal: ".$e -> getMessage());
        return false;
    } catch (PDOException $pdoE) {
        error_log("PDO error deleting goal: ".$pdoE -> getMessage());
        return false;
    }
}

function completeGoal($pdo, $goalId) {
    try {
        $stmt = $pdo -> prepare('UPDATE `goal`
                                SET `completionStatus` = :completionStatus
                                WHERE `goal`.`goalId` = :goalId');
        $stmt -> execute([':completionStatus' => 'completed', ':goalId' => $goalId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error updating goal completion status: ".$pdoE -> getMessage());
        return false;
    }
}

function generateGoalId() {
    return '#GL'.strtolower(substr(md5(uniqid()), 0, 5));
}
exit();
?>