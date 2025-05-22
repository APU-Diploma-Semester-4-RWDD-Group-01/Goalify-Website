<?php
session_start();
session_unset();
session_destroy();
header("Location: /Goalify/Pages/admin/login/login.php");
exit();
?>