<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../../includes/db.php';

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $shortTermGoals = getShortTermGoals($pdo, $userId);
    $longTermGoals = getLongTermGoals($pdo, $userId);
    $completedGoals = getCompletedGoals($pdo, $userId);
    echo json_encode(['shortTermGoals' => $shortTermGoals, 'longTermGoals' => $longTermGoals, 'completedGoals' => $completedGoals]);
    exit();
}

function getShortTermGoals($pdo, $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `goal` WHERE `userId` = :userId AND `goalType` = :goalType AND `completionStatus` = :completionStatus;');
        $stmt -> execute([':userId' => $userId, ':goalType' => 'short-term', ':completionStatus' => 'incomplete']);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all short-term goals: ".$pdoE -> getMessage());
        return null;
    }
}

function getLongTermGoals($pdo, $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `goal` WHERE `userId` = :userId AND `goalType` = :goalType AND `completionStatus` = :completionStatus;');
        $stmt -> execute([':userId' => $userId, ':goalType' => 'long-term', ':completionStatus' => 'incomplete']);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all short-term goals: ".$pdoE -> getMessage());
        return null;
    }
}

function getCompletedGoals($pdo, $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM `goal` WHERE `userId` = :userId AND `completionStatus` = :completionStatus;');
        $stmt -> execute([':userId' => $userId, ':completionStatus' => 'completed']);
        $result = $stmt -> fetchAll();
        return $result;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all short-term goals: ".$pdoE -> getMessage());
        return null;
    }
}
?>