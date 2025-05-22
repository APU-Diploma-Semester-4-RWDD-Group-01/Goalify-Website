<!--
Author: Phang Shea Wen
Date: 2025/02/01
Filename: table.php
Description: Page showing table of project management
            (1) project task, sub-task, assigned members, priority, estimate, assigned date, due date, status
-->
<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';
require_once '../../../../includes/session.php';
require_once '../../../../includes/userFunction.php';

if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    header("location: $path");
    exit();
}
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');

if (isset($_SESSION['project_id'])) {
    $userId = $_SESSION['user_id'];
    $workspaceId = $_SESSION['workspace_id'];
    $selectedProjectId = $_SESSION['project_id'];
}

$workspaceName = getWorkspaceDetails($pdo, $workspaceId, 'workspaceName');
$projectName = getProjectDetails($pdo, $selectedProjectId, 'projectName');
$owner = getOwner($pdo, $workspaceId);
$allMembers = getMembers($pdo, $workspaceId);
    // require '../../../../includes/db.php';
    // include 'PHP/loadProjectTask.php';
    // require '../../workspace.php';
?>

<script>
    var deviceTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
</script>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace</title>
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../../global/global.css">
    <link rel="stylesheet" href="../../workspaces.css">
    <link rel="stylesheet" href="table.css">
    <title>Workspaces</title>
    <script src="../../../global/JS/theme.js" defer></script>
    <script src="../../../global/JS/sideNav.js" defer></script>
    <script src="../../../global/JS/workspace.js" defer></script>
    <script src="../../../global/JS/global.js" defer></script>
    <!-- Notification -->
    <link rel="stylesheet" href="../../../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>

    <script src="JS/loadProjectTask.js" defer></script>
    <script src="JS/projectTaskOverlay.js" defer></script>
    <script src="JS/projectTask.js" defer></script>
</head>
<body>
    <?php include '../../../../includes/navigation.php'; ?>
    <?php include '../../../notification/notification.php' ?>
    <nav class="side-nav">
        <span class="material-symbols-rounded expand">chevron_right</span>
        <ul>
            <div id="toggle-side-nav"><h2 id="side-nav-workspace-name"><?php echo $workspaceName ?></h2><span class="material-symbols-rounded close">chevron_left</span></div>
            <hr>
            <a href="../../main/projects/projects.php"><li id="workspace-project">Projects</li></a>
            <hr>
            <a href="../../main/members/members.php"><li id="workspace-member">Members</li></a>
            <hr>
            <a href="../../main/description/description.php"><li id="workspace-description">Description</li></a>
            <br>
            <div class="projects">
                <h2 id="side-nav-project-name"><?php echo $projectName; ?></h2>
                <hr>
                <a href="../description/projectDescription.php"><li id="project-description">Description</li></a>
                <hr>
                <a href="table.php"><li id="project-table">Table</li></a>
                <hr>
                <a href="../sharedFiles/sharedFiles.php"><li id="project-file">Shared Files</li></a>
                <hr>
                <a href="../calendar/calendar.php"><li id="project-calendar">Calendar</li></a>
            </div>
        </ul>
    </nav>
    <div class="content">
        <div class="task-heading">
            <h1></h1>
            <div id="add-project-task-button"><span class="material-symbols-rounded expand">add</span><p  id="new-task-text">New Task</p> <span class="material-symbols-rounded expand">table</span></div>
        </div>
        <hr>
        <div class="task-list">
        </div>
    </div>
</body>
</html>