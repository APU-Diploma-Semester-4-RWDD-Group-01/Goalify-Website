<?php
    session_start();
    if (!isset($_SESSION["user_id"])) {
        $path = "/Goalify/Pages/admin/login/login.php";
        header("location: $path");
        exit();
    }
    require("../../../includes/session.php");
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
    <link rel="stylesheet" href="../../global/global.css">
    <link rel="stylesheet" href="createUser.css">
    <link rel="stylesheet" href="../userManagement.css">
    <script src="../../global/JS/navigation.js" defer></script>
    <!-- <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> -->
    <style>
        h1, h2 {
            margin-bottom: 30px;
        }
        h2 {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <?php include '../../../includes/adminNavigation.php'?>
    <div class="content">
        <h1>Create New User</h1>
        <form class="user-details" action="#" method="POST">
            <label for="create-user-name">User Name</label>
            <input name="create-user-name" id="create-user-name" type='text' autocomplete="off" placeholder="John Doe">

            <label for="create-user-email">Email</label>
            <input id="create-user-email" placeholder="john@gmail.com" name="create-user-email">

            <label for="create-user-password">Password</label>
            <input id="create-user-password" type="password" placeholder="Type your password" name="create-user-password">
            <div class="error-msg"></div>
            <div class="button">
                <a type="button" id="cancel-create-user-button" href="../userManagement.php">Back</a>
                <button type="submit" id="create-user-button">Create</button>
            </div>
        </form>
    </div>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        // topNavTitle.textContent = 'User Management / Create New User';
        topNavTitle.textContent = 'User Management';

        const createUserErrorDiv = document.querySelector('form.user-details div.error-msg');
        const createUserForm = document.querySelector('form.user-details');
        console.log(createUserForm);
        // const createButton = document.getElementById('create-user-button');
        const createErrorDiv = document.querySelector('div.error-msg');
        createUserForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const createUserFormData = new FormData(createUserForm);
            fetch('/Goalify/Pages/includes/userHandler.php', {
                method: 'POST',
                body: createUserFormData
            })
            .then(response => response.json())
            .then(response => {
                if (response.register) {
                    alert(response.msg);
                    createUserForm.reset();
                    createErrorDiv.replaceChildren();
                } else {
                    const userEmail = document.querySelector('input#create-user-email');
                    const userPwd = document.querySelector('input#create-user-password');
                    userEmail.value = '';
                    userPwd.value = '';
                    createErrorDiv.innerHTML = `
                    <span class="material-symbols-rounded">error</span>
                    <p>${response.msg}</p>
                    `;
                }
            })
        })
    })
</script>