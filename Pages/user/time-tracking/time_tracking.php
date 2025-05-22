<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}

require('../../includes/db.php');
require('../../includes/session.php');
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=trending_flat" />
    <!-- <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=error" /> -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="time_tracking.css">
    <link rel="stylesheet" href="../notification/notification.css">
    <title>TimeTracking</title>
    <script src="../global/JS/theme.js" defer></script>
    <script src="../global/JS/sideNav.js" defer></script>
    <script src="../global/JS/workspace.js" defer></script>
    <script src="../global/JS/global.js" defer></script>
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <script src="time_tracking.js" defer></script>
</head>

<body>
    <?php include '../notification/notification.php' ?>
    <?php include '../../includes/navigation.php' ?>
    <div class="timer-main">
        <div class="timer-side-nav">
            <div class="timer-close-nav">
                <ul>
                    <li id="tasks"><span><i class="fa-solid fa-list-ul fa-xl"></i></i></span>Tasks</li>
                    <li id="records"><span><i class="fa-regular fa-file-lines fa-xl"></i></span>Records</li>
                </ul>
            </div>
            <div class="timer-expand-nav">
                <span id="xmark"><i class="fa-solid fa-xmark"></i></span>
                <div class="tasklist">
                    <h1>Task Lists</h1>
                    <ul class="task">
                    </ul>
                    <div id="confirm-box">
                        <p id="task-confirm-message">Select this task?</p>
                        <span id="task-check"><i class="fa-solid fa-check fa-xl" style="color: #20df73;"></i></span>
                        <span id="task-xmark"><i class="fa-solid fa-xmark fa-xl" style="color: #ff0019;"></i></span>
                    </div>
                </div>
                <div class="record">
                    <h1>Focus Records</h1>
                    <div id="record-date"><i class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;<span
                            id="date-text"></span></div>
                    <div id="table-wrapper">
                        <table id="record-list">
                        </table>
                    </div>
                    <div id="duration-notify-message">** The start and end times include pause, but the duration
                        reflects only the actual work time.</div>
                    <div id="no-record">- No record is available here :D -</div>
                </div>
            </div>
        </div>
        <div class="timer-content">
            <div class='task-name'>
                No Task Selected
                <span id="chevron-down"><i class="fa-solid fa-chevron-down fa-xs" style="color: #808080;"></i></span>
            </div>
            <div class='donut-preset-container-wrapper'>
                <div class="donut">
                    <svg width="370" height="370" id="circle">
                        <circle id="progress-ring-bottom" cx="185" cy="185" r="165" stroke="#dddddd" stroke-width="15"
                            fill="none" />
                        <circle id="progress-ring" cx="185" cy="185" r="165" stroke-width="15" fill="none"
                            stroke-dasharray="1036.7" stroke-dashoffset="0" stroke-linecap="round"
                            transform="rotate(-90 185 185)" />
                    </svg>
                    <form action="" method="post" id="timer-form">
                        <input type="number" class="timer" id="hour" placeholder="00">
                        <span id="symbol1">:</span>
                        <input type="number" class="timer" id="minute" placeholder="00">
                        <span id="symbol2">:</span>
                        <input type="number" class="timer" id="second" placeholder="00">
                    </form>
                </div>
                <div id="preset-container">
                    <button class="preset" id="timer1">00:10:00</button>
                    <button class="preset" id="timer2">00:30:00</button>
                    <button class="preset" id="timer3">00:15:00</button>
                    <button class="preset" id="timer4">00:05:00</button>
                </div>
            </div>
            <div class="startbutton">
                <button id="startfocus" class="button">Start Focus</button>
            </div>
            <div class="otherbutton">
                <button id="endfocus" class="button">End Focus</button>
                <button id="pause" class="button">Pause</button>
                <button id="reset" class="button">Restart</button>
                <button id="resume" class="button">Resume</button>
            </div>
        </div>
    </div>

</body>

</html>