<?php
    require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';
    session_start();
    if (!isset($_SESSION["user_id"])) {
        $path = "/Goalify/Pages/admin/login/login.php";
        header("location: $path");
        exit();
    }
    require("../../includes/session.php");
    sessionTimeOut("/Goalify/Pages/admin/login/login.php");
    $userId = $_SESSION['user_id'];
    $selectedUserId = $_GET['userId'];
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
    <link rel="stylesheet" href="../productivity/productivity_details.css">
    <script src="../global/JS/navigation.js" defer></script>
    <script src="productivity_details.js" defer></script>
    <title>Productivity Details</title>
</head>

<body>
    <?php include '../../includes/adminNavigation.php' ?>
    <div class="content">
        <div class="user-container">
            <!-- <div class="user">
                <div class="profile-img"></div>
                <div class="user-info">
                    <p class="name">Nie Nie</p>
                    <p class="email">Email : nienie@gmail.com</p>
                    <p class="status">Account status : Active</p>
                </div>
            </div> -->
        </div>
        <div class="productivity-content">
            <div class="workspace">
                <h1 class="workspace-title">Workspace Involved (2)</h1>
                <table class="workspace-table table">
                </table>
            </div>

            <div class="projects">
                <h1 class="project-title">Project Involved (2)</h1>
                <table class="project-table table">
                </table>
            </div>
            <div class="personal-task">
                <h1 class="task-title">Personal Task (2)</h1>
                <table class="task-table table">
                </table>
            </div>
        </div>
        <a href="productivity.php" class="back">Back to Productivity Metrics</a>
    </div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        topNavTitle.textContent = "Productivity Metrics";
        // uncomment if want to show history tab
        // topNavTitle.textContent = "Productivity Metrics / <?php echo getUserName($pdo, $selectedUserId) ?>'s Productivity Details";
    })
</script>