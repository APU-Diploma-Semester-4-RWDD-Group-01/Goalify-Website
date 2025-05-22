<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        $path = "/Goalify/Pages/admin/login/login.php";
        header("location: $path");
        exit();
    }
    require("../../includes/session.php");
    sessionTimeOut("/Goalify/Pages/admin/login/login.php");
    $userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Metrics</title>
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="userMetrics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../global/JS/navigation.js" defer></script>
    <script src="userMetrics.js" defer></script>
</head>
<body>
    <?php include '../../includes/adminNavigation.php' ?>
    <div class="content">
        <h1>User Metrics</h1>
        <div class="summary">
            <h2 id="summary-title">Summary</h2>
            <div class="user-statistics">
                <div class="col-1" id="total-users">
                    <p class="title">Total Users</p>
                    <p class="data" id="total-users-count">Loading...</p>
                </div>
                <div class="col-2" id="sign-ups">
                    <p class="title">Sign-ups</p>
                    <div class="data" id="chart-container">
                        <canvas id="signupsChart"></canvas>
                    </div>
                </div>
                <div class="col-3" id="active-users">
                    <p class="title">Active Users</p>
                    <div class="data" id="chart-container">
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="registered-users-section">
            <div class="registered-user-container">
                <h2><span id="registered-title"></span></h2>
                <form action="#" method="POST" id="search-users-bar">
                    <button type="submit" class="material-symbols-rounded">search</button>
                    <input type="search" name="search-users-input" id="search-users-input" placeholder="Search user..." autocomplete="off">
                </form>
            </div>
            <div class="registered-users-list">
            </div>
            <a href="#" class="more-users">More...</a>
        </div>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        topNavTitle.textContent = "User Metrics";
    })
</script>