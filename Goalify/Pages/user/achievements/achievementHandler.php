<?php
include '../../includes/db.php';

header("Content-Type: application/json");
ob_clean();

// ---------------------------------- Fetch Quote ----------------------------------
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["quote"])) {
    $quoteApiUrl = "https://zenquotes.io/api/random";
    $quoteData = @file_get_contents($quoteApiUrl);
    $decodedData = json_decode($quoteData, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedData) && isset($decodedData[0])) {
        // Remove the 'h' field from the API response
        unset($decodedData[0]['h']);
        header("Content-Type: application/json");
        echo json_encode($decodedData[0]); // Send only the first quote without 'h'
    } else {
        error_log("Invalid API response: " . $quoteData);
        echo json_encode(["error" => "Invalid API response"]);
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['userId'])) {
        echo json_encode(["error" => "User ID missing"]);
        exit();
    }
    $userId = $_GET['userId'];
    $month = isset($_GET["month"]) ? (int)$_GET["month"] : date("m");
    $year = isset($_GET["year"]) ? (int)$_GET["year"] : date("Y");
    $response = [];

    // ---------------------------------- Fetch Focus Time Data (Record Section) ----------------------------------
    $sqlFocus = 
        "SELECT t.category, 
            COUNT(DISTINCT f.taskId) AS totalTasks, 
            SUM(f.duration) AS totalTimeSpent, 
            SUM(f.duration) / COUNT(DISTINCT f.taskId) AS avgTimeSpent
        FROM focusrecord AS f 
        JOIN task t ON f.taskId = t.task_id
        WHERE MONTH(f.timeTrackingDate) = :month
        AND YEAR(f.timeTrackingDate) = :year
        AND f.userId = :userId
        GROUP BY t.category";

    $stmtFocus = $pdo->prepare($sqlFocus);
    $stmtFocus->bindParam(':month', $month, PDO::PARAM_INT);
    $stmtFocus->bindParam(':year', $year, PDO::PARAM_INT);
    $stmtFocus->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmtFocus->execute();
    $response["focusData"] = $stmtFocus->fetchAll(PDO::FETCH_ASSOC);

    // ---------------------------------- Fetch Completed Task Count (Chart Section) ----------------------------------
    $sqlCompleted = 
        "SELECT t.category, COUNT(*) AS totalCompletedTasks
        FROM task t
        WHERE t.complete_status = 'done'
        AND MONTH(t.completed_date) = :month
        AND YEAR(t.completed_date) = :year
        AND t.userId = :userId
        GROUP BY t.category
        ORDER BY totalCompletedTasks DESC";

    $stmtCompleted = $pdo->prepare($sqlCompleted);
    $stmtCompleted->bindParam(':month', $month, PDO::PARAM_INT);
    $stmtCompleted->bindParam(':year', $year, PDO::PARAM_INT);
    $stmtCompleted->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmtCompleted->execute();
    $response["completedTasks"] = $stmtCompleted->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($response);
}
?>
