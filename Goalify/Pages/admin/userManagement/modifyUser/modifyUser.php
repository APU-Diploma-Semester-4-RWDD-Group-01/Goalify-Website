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

require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';
$userId = $_GET['userId'];
$userName = getUserName($pdo, $userId);
$userEmail = getUserEmail($pdo, $userId);
?>

<script>
    console.log("<?php echo $userId; ?>")
    console.log("<?php echo $userEmail; ?>")
</script>

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
    <link rel="stylesheet" href="../createUser/createUser.css">
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
        p.modify-pwd-info {
            margin: 5px 10px;
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.534);
        }
    </style>
</head>
<body>
    <?php include '../../../includes/adminNavigation.php'?>
    <div class="content">
        <h1>Modify User</h1>
        <form class="user-details" action="#" method="POST" data-user-id="<?php echo $userId ?>">
            <label for="modify-user-name">User Name</label>
            <input name="modify-user-name" id="modify-user-name" type='text' autocomplete="off" placeholder="John Doe" value="<?php echo $userName ?>">

            <label for="modify-user-email">Email</label>
            <input type="email" id="modify-user-email" placeholder="john@gmail.com" name="modify-user-email" value="<?php echo $userEmail ?>">

            <label for="modify-user-password">Password</label>
            <input id="modify-user-password" type="password" placeholder="Type your password" name="modify-user-password" value="">
            <p class="modify-pwd-info">*Fill in new password to modify</p>
            <div class="error-msg"></div>
            <div class="button">
                <a type="button" id="cancel-modify-user-button" href="../userManagement.php">Back</a>
                <button type="submit" id="modify-user-button">Save</button>
            </div>
        </form>
    </div>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const topNavTitle = document.querySelector('nav#top-nav .left .history-tab p');
        // topNavTitle.textContent = 'User Management / Modify User';
        topNavTitle.textContent = 'User Management';

        const modifyUserErrorDiv = document.querySelector('form.user-details div.error-msg');
        const modifyUserForm = document.querySelector('form.user-details');
        // const createButton = document.getElementById('modify-user-button');
        const modifyErrorDiv = document.querySelector('div.error-msg');
        const userName = document.querySelector('input#modify-user-name');
        const userEmail = document.querySelector('input#modify-user-email');
        const userPwd = document.querySelector('input#modify-user-password');
        modifyUserForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const modifyUserFormData = new FormData(modifyUserForm);
            const userId = modifyUserForm.getAttribute('data-user-id');
            modifyUserFormData.append('user-id', userId);

            fetch('/Goalify/Pages/includes/userHandler.php', {
                method: 'POST',
                body: modifyUserFormData
            })
            .then(response => response.json())
            .then(response => {
                if (response.update) {
                    alert(response.msg);
                    userName.value = response.userName;
                    userEmail.value = response.userEmail;
                    userPwd.value = '';
                    modifyErrorDiv.replaceChildren();
                } else {
                    userPwd.value = '';
                    modifyErrorDiv.innerHTML = `
                    <span class="material-symbols-rounded">error</span>
                    <p>${response.msg}</p>
                    `;
                }
            })
        })
    })
</script>