<?php
    include '../notification/notification.php';
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
    <link rel="stylesheet" href="achievements.css">
    <title>Achievements</title>
    <script src="../global/JS/theme.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="achievements.js" defer></script>
    <script src="../global/JS/workspace.js" defer></script>
    <!-- Notification -->
    <link rel="stylesheet" href="../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
</head>
<body>
    <?php include '../../includes/navigation.php' ?>
    <input type="hidden" id="user-id" value="<?php echo htmlspecialchars($userId); ?>">
    <!-- encodeURIComponent() - passing user input via fetch() if it contains special characters -->
    <!-- for js to get userid -->
    <div class="content">
        <div class="record-section">
            <p>Record</p>
            <ul class="record-list">
            </ul>
        </div>
        <div class="date-selection">
            <span class="material-symbols-rounded arrowleft" id="prev-month">chevron_left</span>
            <span class="chosen_date" id="selected-date">February 2025</span>
            <span class="material-symbols-rounded arrowright" id="next-month">chevron_right</span>
        </div>
        <div class="chart-section">
            <p>Achievements Chart</p>
            <div class="chart">
                <svg id="pie-chart" viewBox="0 0 100 100" width="200" height="200"></svg>
                <div class="tooltip" id="pie-tooltip" style="display: none;"></div>
            </div>
        </div>
        <div class="category-section">
            <div class="category">
                <div class="round-category" id="category-important-urgent" data-category="Urgent" style="background-color: var(--red-color);"></div>
                <div class="category-percentage" id="urgent-count">0 tasks</div>
            </div>
            <div class="divider">|</div>
            <div class="category">
                <div class="round-category" id="category-important-not-urgent" data-category="Plan Ahead" style="background-color: var(--orange-color);"></div>
                <div class="category-percentage" id="plan-ahead-count">0 tasks</div>
            </div>
            <div class="divider">|</div>
            <div class="category">
                <div class="round-category" id="category-urgent-not-important" data-category="Handle Fast" style="background-color: var(--yellow-color);"></div>
                <div class="category-percentage" id="handle-fast-count">0 tasks</div>
            </div>
            <div class="divider">|</div>
            <div class="category">
                <div class="round-category" id="category-not-important-not-urgent" data-category="On Hold" style="background-color: var(--green-color);"></div>
                <div class="category-percentage" id="on-hold-count">0 tasks</div>
            </div>
        </div>
        <div class="quote-section">
            <p>Quote</p>
            <div class="quote">Loading...</div>
        </div>
    </div>
</body>
</html>
