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
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/session.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/user/workspaces/workspace.php';

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
    <link rel="stylesheet" href="sharedFiles.css">
    <title>Workspaces</title>
    <script src="../../../global/JS/theme.js" defer></script>
    <script src="../../../global/JS/sideNav.js" defer></script>
    <script src="../../../global/JS/workspace.js" defer></script>
    <script src="../../../global/JS/global.js" defer></script>
    <script src="actions.js" defer></script>
    <script src="JS/fileHandler.js" defer></script>
    <!-- Notification -->
    <link rel="stylesheet" href="../../../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
    <script src="JS/loadFile.js" defer></script>
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
                <a href="../table/table.php"><li id="project-table">Table</li></a>
                <hr>
                <a href="../sharedFiles/"><li id="project-file">Shared Files</li></a>
                <hr>
                <a href="../calendar/calendar.php"><li id="project-calendar">Calendar</li></a>
            </div>
        </ul>
    </nav>
    <div class="content">
        <div class="file-heading">
            <h1>Files</h1>
            <div class="file-action-button">
                <!-- <div id="create-folder-button"><span class="material-symbols-rounded expand">folder</span><p  id="create-folder-text">Create Folder</p></div> -->
                <div id="upload-file-button"><span class="material-symbols-rounded expand">draft</span><p  id="create-file-text">Upload File</p></div>
            </div>
        </div>
        <div class="table-rows" data-project-id="<?php echo $selectedProjectId ?>">
            <div class="row file-header">
                <span class="col-1-h s-col-1-h material-symbols-rounded">draft</span>
                <div class="col-2-h s-col-2-h file-name">Name</div>
                <div class="col-3-h s-col-3-h"></div>
                <div class="col-4-h s-col-4-h uploaded-by">Uploaded by</div>
                <div class="col-5-h s-col-5-h uploaded">Uploaded</div>
            </div>
            <div class="rows"></div>
        </div>
    </div>
</body>
</html>

<script>
    function updateFileName() {
    let fileInput = document.getElementById("new-file");
    let fileName = document.getElementById("file-name");
    fileName.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : "No file chosen";
}
</script>