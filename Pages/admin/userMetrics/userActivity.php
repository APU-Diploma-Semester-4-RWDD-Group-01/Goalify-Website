<?php
    session_start();
    require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';
    if (!isset($_SESSION["user_id"])) {
        $path = "/Goalify/Pages/admin/login/login.php";
        header("location: $path");
        exit();
    }
    require("../../includes/session.php");
    sessionTimeOut("/Goalify/Pages/admin/login/login.php");
    $userId = $_SESSION['user_id'];
    $selectedUserId = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="../userMetrics/userActivity.css">
    <script src="../global/JS/navigation.js" defer></script>
    <script src="userActivity.js" defer></script>
    <title>User Activity</title>
</head>
<body>
    <?php include '../../includes/adminNavigation.php' ?>
    <div class="content">
        <div class="user-details">
            <h1 id="title"></h1>
            <p>User ID: <span id="user-id">Loading...</span></p>
            <p>Date Joined: <span id="date-joined">Loading...</span></p>
        </div>
        <div class="activity-log">
            <table class="activity-table">
            </table>
        </div>
        <br><br>
        <div class="back-container">
            <a id="back-to-details" class="back">Back to User Details</a>
        </div>
        
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        topNavTitle.textContent = "User Metrics";
        // uncomment if want to show history tab
        // topNavTitle.textContent = "User Metrics / <?php echo getUserName($pdo, $selectedUserId) ?>'s User Details / <?php echo getUserName($pdo, $selectedUserId) ?>'s Activity Log";
    })
</script>