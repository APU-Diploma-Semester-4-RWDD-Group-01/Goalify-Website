<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        $path = "/Goalify/Pages/user/login & register/login.php";
        header("location: $path");
        exit();
    }
    require("../../includes/session.php");
    sessionTimeOut("/Goalify/Pages/user/login & register/login.php");
    $userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="goal.css">
    <title>Goal</title>
    <script src="../global/JS/theme.js" defer></script>
    <script src="../global/JS/workspace.js" defer></script>
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
    <script src="JS/goalHandler.js" defer></script>
    <script src="JS/loadGoal.js" defer></script>
    <!-- Notification -->
    <link rel="stylesheet" href="../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
</head>
<body>
    <?php include '../../includes/navigation.php' ?>
    <?php include '../notification/notification.php' ?>
    <div class="content">
        <div class="main-content">
            <div class="short-term-section">
                <div class="heading">
                    <span class="material-symbols-rounded">hourglass_top</span>
                    <p>Short Term Goal</p>
                </div>
                <div class="short-term-list">Loading...</div>
                <div class="button-div add-short-term-goal-button-div">
                    <button class="add-goal" id="add-short-term-goal"><span class="material-symbols-rounded expand">add</span>Add Goal</button>
                </div>
            </div>
            <div class="long-term-section">
                <div class="heading">
                    <span class="material-symbols-rounded">track_changes</span>
                    <p>Long Term Goal</p>
                </div>
                <div class="long-term-list">Loading...</div>
                <div class="button-div add-long-term-goal-button-div">
                    <button class="add-goal" id="add-long-term-goal"><span class="material-symbols-rounded">add</span>Add Goal</button>
                </div>
            </div>
            <div class="completed-section">
                <div class="heading">
                    <span class="material-symbols-rounded">celebration</span>
                    <p>Completed</p>
                </div>
                <div class="completed-list">Loading...</div>
            </div>
        </div>
    </div>
</body>
</html>