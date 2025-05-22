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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="userManagement.css">
    <script src="../global/JS/navigation.js" defer></script>
    <script src="action.js" defer></script>
    <!-- <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> -->
    <style>
        h1, h2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        h2 {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <?php include '../../includes/adminNavigation.php'?>
    <?php include 'PHP/loadUsers.php' ?>
    <div class="content">
        <h1>User Management</h1>
        <div class="all-users-section">
                <div class="all-users-title"><h2>Registered User</h2><p id="num-users"></p></div>
                <div class="search-users">
                    <a id="create-user-button" href="createUser/createUser.php"><span class="material-symbols-rounded">add</span><p>New User</p></a>
                    <form action="#" method="POST" id="search-users-bar">
                        <button type="submit" class="material-symbols-rounded" style="cursor: pointer;">search</button>
                        <input type="search" name="search-users-input" id="search-users-input" placeholder="Search user..." autocomplete="off">
                    </form>
                </div>
        </div>
        <div class="user-list" data-page="1" onchange="loadUsers()"></div>
        <div class="pagination"><ul></ul></div>
    </div>
    <!-- <script src="pagination.js"></script> -->
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        topNavTitle.textContent = 'User Management';
    })
</script>