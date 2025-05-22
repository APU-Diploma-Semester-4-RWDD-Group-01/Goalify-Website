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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contactButton = document.querySelector('.contact-button');
        contactButton.addEventListener('click', () => {
            if (!document.body.contains(document.querySelector('#overlay'))) {
                createOverlay(
                    'contact-support',
                    `
                    <p><strong>Contact Support</strong></p>
                    <p><span class="material-symbols-outlined">mail</span><a href="mailto:support@goalify.com">admin@goalify.com</a></p>
                    <p><span class="material-symbols-outlined">call</span><a href="tel:+1234567890"> +123 456 7890</a></p>
                    <div class="button">
                        <button type="button" id="close-contact">Close</button>
                    </div>
                    `
                );
                document.querySelector('#close-contact').addEventListener('click', () => {
                    removeOverlay('contact-support');
                });
            }
        });
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link id="favicon" rel="icon" type="image/png" href="/Goalify/Img/goalify_favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../global/global.css">
    <link rel="stylesheet" href="contactSupport.css">
    <title>Contact Support</title>
    <script src="../global/JS/theme.js" defer></script>
    <script src="../global/JS/workspace.js" defer></script>
    <!-- Notification -->
    <link rel="stylesheet" href="../notification/notification.css">
    <script src="/Goalify/Pages/user/global/JS/notification.js" defer></script>
    <!-- Profile -->
    <script src="/Goalify/Pages/user/global/JS/top_nav_profile_pic.js" defer></script>
</head>
<body>
    <?php include '../../includes/navigation.php' ?>
    <div class="content">
        <div class="header">
            <p>FAQ</p>
        </div>
        <div class="faq-topics">
            <div class="faq-section">
                <div class="topic" id="topic-general">
                    <p class="topic-title">About Goalify</p>
                    <ol>
                        <li class="question">What is Goalify?</li>
                        <p class="answer">Goalify is a web application designed to help users achieve goals effectively, efficiently, and productively.</p>
                        <br>
                        <li class="question">Can I use Goalify on mobile devices?</li>
                        <p class="answer">Yes, Goalify is a responsive web app that works on both desktop and mobile browsers.</p>
                    </ol>
                </div>
                <div class="topic" id="topic-account">
                    <p class="topic-title">Account & Login</p>
                    <ol>
                        <li class="question">How do I create an account?</li>
                        <p class="answer">Click on the Get Started button on the homepage, then go to the login page and click on register.</p>
                        <br>
                        <li class="question">Can I update my profile picture?</li>
                        <p class="answer">Yes, you can upload a new profile picture in the Profile. Just select an image and save the changes.</p>
                    </ol>
                </div>
                <div class="topic" id="topic-workspace">
                    <p class="topic-title">Workspace & Collaboration</p>
                    <ol>
                        <li class="question">How do I invite team members to my workspace?</li>
                        <p class="answer">Go to Workspace > Members > + New Member, enter their user ID, and send an invite.</p>
                        <br>
                        <li class="question">How many members can I invite to a workspace?</li>
                        <p class="answer">There is no limit to the number of members you can invite to a workspace. You can add as many collaborators as needed to manage your projects efficiently.</p>
                    </ol>
                </div>
                <div class="topic" id="topic-technical">
                    <p class="topic-title">Technical Issues & Support</p>
                    <ol>
                        <li class="question">The website is not loading properly. What should I do?</li>
                        <p class="answer">Try clearing your browser cache or using a different browser. If the issue persists, contact support.</p>
                        <br>
                        <li class="question">Is my data safe?</li>
                        <p class="answer">Yes, we use secure encryption to protect all user data.</p>
                    </ol>
                </div>
            </div>
        </div>
        <div class="furtherHelp">
            <p class="help-title">"Still have questions? We're here to help!"</p>
            <p class="details">If you have any additional enquiries or need assistance, please don't hesitate to contact our admin team.</p>
            <button class="contact-button">Contact</button>
        </div>
    </div>
</body>
</html>
