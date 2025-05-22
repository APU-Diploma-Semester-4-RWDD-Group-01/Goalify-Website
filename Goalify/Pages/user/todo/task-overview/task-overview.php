<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        $path = "/Goalify/Pages/user/login & register/login.php";
        header("location: $path");
        exit();
    }
    require("../../../includes/session.php");
    sessionTimeOut("/Goalify/Pages/user/login & register/login.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link rel="stylesheet" href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../../notification/notification.css">
    <link rel="stylesheet" href="../../global/global.css">
    <link rel="stylesheet" href="../todo.css">
    <link rel="stylesheet" href="task-overview.css">
    <title>Task Overview</title>
    <script src="../../global/JS/theme.js" defer></script>
    <script src="../../global/JS/sideNav.js" defer></script>
    <script src="../../global/JS/workspace.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="task-overview.js" defer></script>
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    
</head>
<body>
    <?php include '../../../includes/navigation.php'?>
    <?php include '../../notification/notification.php' ?>
    
    <nav class="side-nav">
        <span class="material-symbols-rounded expand">chevron_right</span>
        <ul>
            <div id="toggle-side-nav"><h2>To-Do List</h2><span class="material-symbols-rounded close">chevron_left</span></div>
            <hr>
            <a href="task-overview.php"><li id="toggle-task-overview">Task Overview</li></a>
            <hr>
            <a href="../timeline/timeline.php"><li id="toggle-timeline">Timeline</li></a>
            <hr>
            <a href="../task-status/task-status.php"><li id="toggle-task-status">Task Status</li></a>
        </ul>
    </nav>
    
    <div class="content">

        <div id="task-overview" class="active">
            
            <span class="material-symbols-rounded arrowleft">arrow_left</span><h4 class="chosen_date"></h4><span class="material-symbols-rounded arrowright">arrow_right</span>
            <div id="task-categories">
                <div class="category" id="important-urgent" data-category="urgent">
                    <strong>⏰ Urgent ⏰</strong>
                    <br>
                    <div class="task-content"></div>
                    <button class="to-do-button"><span>+</span> Add To-do</button>
                </div>

                <div class="category" id="important-not-urgent" data-category="plan ahead">
                    <strong>📝 Plan Ahead 📝</strong>
                    <br>
                    <div class="task-content"></div>
                    <button class="to-do-button"><span>+</span> Add To-do</button>
                </div>

                <div class="category" id="urgent-not-important" data-category="handle fast">
                    <strong>⚡ Handle Fast ⚡</strong>
                    <br>
                    <div class="task-content"></div>
                    <button class="to-do-button"><span>+</span> Add To-do</button>
                </div>

                <div class="category" id="not-urgent-not-important" data-category="on hold">
                    <strong>💤 On Hold 💤</strong>
                    <br>
                    <div class="task-content"></div>
                    <button class="to-do-button"><span>+</span> Add To-do</button>
                </div>
            </div>
        </div>

    </div>
    
</body>
</html>
