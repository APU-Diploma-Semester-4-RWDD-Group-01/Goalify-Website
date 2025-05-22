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
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="../productivity/productivity.css">
    <script src="../global/JS/navigation.js" defer></script>
    <script src="productivity.js" defer></script>
    <title>Productivity Metrics</title>
</head>
<body>
    <?php include '../../includes/adminNavigation.php' ?>
    <div class="content">
        <div class="title">
            <h1>Productivity Metrics</h1>
        </div>
        <div class="content-1">
            <div class="col-1" id="active-workspace overview">
                <p class="workspace-title">Active Workspaces</p>
                <p class="workspace-data">100</p>
                <p class="workspace-growth-rate">+25% from last month</p>
            </div>
            <div class="col-2" id="active-project overview">
                <p class="project-title">Projects Completed</p>
                <p class="project-data">50</p>
                <p class="project-growth-rate">+25% from last month</p>
            </div>
        </div>
        <br>
        <div class="content-2">
            <div class="content-2-topbar">
                <span id="registered-title"></span>
                <form action="#" method="POST" id="search-users-bar">
                    <button type="submit" class="material-symbols-rounded">search</button>
                    <input type="search" name="search-users-input" id="search-users-input" placeholder="Search user..." autocomplete="off">
                </form>
            </div>
            <div class="user-list">
                <!-- <div class="user">
                    <div class="profile-img"></div>
                    <span class="name">Nie Nie</span>
                    <span class="view-details"><a href="productivity_details.php">View Details</a></span>
                </div> -->
            </div>
            <a href="#" class="more-users">More</a>
        </div>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        topNavTitle.textContent = 'Productivity Metrics';
    })
</script>