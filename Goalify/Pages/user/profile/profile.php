<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        $path = "/Goalify/Pages/user/login & register/login.php";
        header("location: $path");
        exit();
    }
    require("../../includes/session.php");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css">
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="../notification/notification.css">
    <title>User Profile</title>
    <script src="../global/JS/theme.js" defer></script>
    <script src="../global/JS/workspace.js" defer></script>
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    
</head>
<body>
    <?php include '../../includes/navigation.php'?>
    <?php include '../notification/notification.php' ?>
    <div class="content">

        <button type="submit" id="save"><strong>Save changes</strong></button>
        
        <div id="user-profile-pic">
            <img src="/Goalify/Img/default_profile.png" alt="user profile"><i class="ri-image-edit-fill camera"></i>
            <input type="file" id="image_input" accept="image/jpg, image/png, image/jpeg, image/svg+xml">
        </div>

        <div class="upper-part">
            <span id="username"><input placeholder="Lorem Epsum" maxlength="15" required><span class="material-symbols-rounded edit">edit</span></span>
            <div class="small-description">
                <span id="workspace-enrolled">Workspaces owned: 0</span><span id="project-enrolled">Projects owned: 0</span>
            </div>
        </div>

        <div class="profile-details">

            <label for="display_id">User ID <span class="restrict_edit"><b>(Unable to edit)</b></span></label>
            <input type="text" id="display_id" disabled>
            <label for="email">Email</label>
            <input type="email" id="email" required>

            <a href="#" id="password">Change Password</a>

            
        </div>

        <button id="logout"><strong>Log out</strong></button>
        
    </div>
    <script src="profile.js" defer></script>
</body> 
</html>