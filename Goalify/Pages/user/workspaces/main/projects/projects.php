<!--
Author: Phang Shea Wen
Date: 2025/01/27
Filename: projects.php
Description: Page showing available projects under the selected workspace
-->
<?php
// session_start();
require 'loadProjects.php';
require '../../../../includes/db.php';
require_once '../../workspace.php';
require_once('../../../../includes/session.php');
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/notification/notification.php';
// session_start();
if (!isset($_SESSION['user_id'])) {
    $path = '/Goalify/Pages/user/login & register/login.php';
    $workspaceId = $_SESSION['workspace_id'];
    header("location: $path");
    exit();
}
sessionTimeOut('/Goalify/Pages/user/login & register/login.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../../global/global.css">
    <link rel="stylesheet" href="../../workspaces.css">
    <link rel="stylesheet" href="projects.css">
    <title>Workspaces</title>
    <script src="../../../global/JS/theme.js" defer></script>
    <script src="../../../global/JS/sideNav.js" defer></script>
    <script src="../../../global/JS/global.js" defer></script>
    <script src="../../../global/JS/workspace.js" defer></script>
    <!-- Notification -->
    <link rel="stylesheet" href="../../../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
</head>
<body>
    <?php include '../../../../includes/navigation.php'; ?>
    <nav class="side-nav">
        <span class="material-symbols-rounded expand">chevron_right</span>
        <ul>
            <div id="toggle-side-nav"><h2 id="side-nav-workspace-name"><?php echo getWorkspaceDetails($pdo, $workspaceId, 'workspaceName') ?></h2><span class="material-symbols-rounded close">chevron_left</span></div>
            <hr>
            <a href="../projects/projects.php"><li id="workspace-project">Projects</li></a>
            <hr>
            <a href="../members/members.php"><li id="workspace-member">Members</li></a>
            <hr>
            <a href="../description/description.php"><li id="workspace-description">Description</li></a>
        </ul>
    </nav>
    <div class="content">
        <div class="all-projects" data-workspace-id="W001">
            <h1 id="ongoing-project-heading">Ongoing Projects</h1>
            <div class="project-wrapper ongoing">
                <span class="material-symbols-rounded scroll-left">chevron_left</span>
                <div class="projects"></div>
                <span class="material-symbols-rounded scroll-right">chevron_right</span>
            </div>
            <hr id="ongoing-project-hr">
            <h1 id="pending-project-heading">Pending Projects</h1>
            <div class="project-wrapper pending">
                <span class="material-symbols-rounded scroll-left">chevron_left</span>
                <div class="projects"></div>
                <span class="material-symbols-rounded scroll-right">chevron_right</span>
            </div>
            <hr id="pending-project-hr">
            <div class="all-projects-section">
                <div class="all-projects-title"><h1>All Projects</h1><p id="num-projects"></p></div>
                <div class="search-add-projects">
                    <div id="add-projects-button"><span class="material-symbols-rounded">add</span><p>New Project</p></div>
                    <form action="#" method="POST" id="search-projects-bar">
                        <button type="submit" class="material-symbols-rounded" style="cursor: pointer;">search</button>
                        <input type="search" name="search-projects-input" id="search-projects-input" placeholder="Search project..." autocomplete="off">
                    </form>
                </div>
            </div>
            <div class="projects-list">
                <hr>
            </div>
        </div>
    </div>
</body>
</html>