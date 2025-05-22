<?php
require('../../includes/db.php');
require '../../includes/userFunction.php';
require_once('../../includes/session.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="../global/global.css">
    <title>Goalify Admin Login Page</title>
</head>
<body>
    <div class="login-area">
        <form action="#" method="POST" class="login">
            <img class="goalify-logo" src="/Goalify/Img/goalify_logo.png" alt="goalify_logo" width="130px" height="35px">
            <!-- <h1 id="login-heading">Log In</h1> -->
            <div class="input email">
                <label for="email">Email</label>
                <input id="email" type="email" name="login-email" class="user-email" placeholder="john@gmail.com" required autocomplete="off">
            </div>
            <div class="input password">
                <label for="password">Password</label>
                <input id="password" type="password" name="login-pwd" class="user-password" placeholder="Type your password" required autocomplete="off">
            </div>
            <div class="error-msg"></div>
            <button type="submit" id="login-button">Login</button>
        </form>
    </div>
</body>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginErrorDiv = document.querySelector('form.login div.error-msg');
        const loginForm = document.querySelector('form.login');
        const loginButton = document.getElementById('login-button');
        loginForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const loginFormData = new FormData(loginForm);
            fetch('/Goalify/Pages/includes/adminHandler.php', {
                method: 'POST',
                body: loginFormData
            })
            .then(response => response.json())
            .then(response => {
                if (response.login) {
                    loginButton.innerHTML = `
                    <p>Login Success</p>
                    <span class="material-symbols-rounded">mood</span>
                    `
                    setTimeout(() => {
                        window.location.href = "/Goalify/Pages/admin/dashboard/dashboard.php";
                    }, 3000); // 4s
                } else {
                    const loginEmail = document.querySelector('form.login div.input.email input#email');
                    const loginPwd = document.querySelector('form.login div.input.password input#password');
                    loginEmail.value = '';
                    loginPwd.value = '';
                    loginErrorDiv.textContent = '*' + response.msg;
                }
            })
        })
    });
</script>