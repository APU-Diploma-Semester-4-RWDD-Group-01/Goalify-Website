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
    <title>Dashboard</title>
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="dashboard.css">
    <script src="../global/JS/navigation.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="dashboard.js" defer></script>
</head>

<body>
    <?php include '../../includes/adminNavigation.php' ?>
    <div class="content">
        <h1>Dashboard</h1>
        <div class="user-summary">
            <h2>User Summary</h2>
            <div class="user-statistics">
                <div id="total-users" class="col-1 s-col-1">
                    <p class="title">Total Users</p>
                    <p class="data" id="total-users-count">Loading...</p>
                </div>
                <div id="sign-ups" class="col-2 s-col-2">
                    <p class="title">Sign-ups</p>
                    <div class="data" id="chart-container">
                        <canvas id="signupsChart"></canvas>
                    </div>
                </div>
                <div id="active-users" class="col-3 s-col-3">
                    <p class="title">Active Users</p>
                    <div class="data" id="chart-container">
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="productivity-summary">
            <h2>Productivity Summary</h2>
            <div class="workspace-statistics">
                <h3>Active Workspace Statistic</h3>
                <form action="dashboardHandler.php" method="post">
                    <select name="workspace-filter" id="workspace-filter">
                        <option value="week" selected>By Week</option>
                        <option value="month">By Month</option>
                        <option value="year">By Year</option>
                    </select>
                </form>
                <div id="workspace-chart">
                    <canvas id="workspace"></canvas>
                </div>
            </div>
            <div class="project-statistics">
                <h3>Projects Completed Statistic</h3>
                <form action="dashboardHandler.php" method="post">
                    <select name="project-filter" id="project-filter">
                        <option value="week">By Week</option>
                        <option value="month">By Month</option>
                        <option value="year">By Year</option>
                    </select>
                </form>
                <div id="project-chart">
                    <canvas id="project"></canvas>
                </div>
            </div>
        </div>
    </div>
</body>
</html>