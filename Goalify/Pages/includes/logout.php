<?php
require_once 'logging.php';
require_once 'db.php';
session_start();
$userId = $_SESSION['user_id'];
insertActivityLog($pdo, $userId, 'A002', "Successfully logged out !");
session_unset();
session_destroy();
header("Location: /Goalify/Pages/user/login%20&%20register/login.php");
exit();
?>