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
    <link rel="stylesheet" href="../userMetrics/userDetails.css">
    <script src="../global/JS/navigation.js" defer></script>
    <script src="userDetails.js" defer></script>
    <title>User Details</title>
</head>
<body>
    <?php include '../../includes/adminNavigation.php' ?>
    <div class="content">
        <h1>User Details</h1>
        <div class="user-container"></div>
        <div class="user-activity-content">
            <h2>Activity Data</h2>
            <br>
            <div class="activity-data">
                <p>Last login:<span id="last-login">Loading...</span></p>
                <p>Personal Task Completed: <span id="tasks-completed">Loading...</span></p>
                <p>Workspaces Involved: <span id="workspaces-involved">Loading...</span></p>
                <p>Projects Involved: <span id="projects-involved">Loading...</span></p>
                <p>Number of Projects Completed: <span id="projects-completed">Loading...</span></p>
            </div>
        </div>
        <div class="button">
            <div id="contact-user-button"><p>Contact User</p></div>
        </div>
        <a href="#" class="user-activity">View User's Activity Log</a>
        <a href="userMetrics.php" class="back">Back to User Metrics</a>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        topNavTitle.textContent = "User Metrics";
        // uncomment if want to show history tab
        // topNavTitle.textContent = "User Metrics / <?php echo getUserName($pdo, $selectedUserId) ?>'s User Details";
    })
</script>