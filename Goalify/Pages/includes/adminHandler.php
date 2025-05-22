<?php
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
            $_ENV[trim($key)] = trim($value);
            $_SERVER[trim($key)] = trim($value);
        }
    }
}
ob_start(); // Start output buffering to prevent extra whitespace
header('Content-Type: application/json');
ob_clean(); // Clear any unwanted output before JSON is returned
require 'db.php';
require('session.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login-email']) && isset($_POST['login-pwd'])) {
        $msg = login($pdo);
        error_log($msg);
        if ($msg === "Login Successfully") {
            echo json_encode(['login' => true, 'msg' => $msg]);
            exit();
        } else {
            echo json_encode(['login' => false, 'msg' => $msg]);
            exit();
        }
    }
}

function login($pdo) {
    $email = trim($_POST['login-email']);
    $userEmail = filter_var($email, FILTER_SANITIZE_EMAIL); // Remove illegal characters
    $userPwd = $_POST['login-pwd'];
    $adminEmail = $_ENV['ADMIN_EMAIL'] ?? ''; // fetch data from env
    $adminPassword = $_ENV['ADMIN_PASSWORD'] ?? ''; // fetch data from env
    if ($userEmail === $adminEmail && $userPwd === $adminPassword) {
        createUserSession('admin', 'admin', $pdo, '/Goalify/Pages/admin/dashboard/dashboard.php', false);
        return "Login Successfully";
    } elseif ($userEmail !== $adminEmail) {
        $loginErrorMsg = 'Email not found'; // echo in js, use js to insert error message
        return $loginErrorMsg;
    } else {
        $loginErrorMsg = 'Invalid password';
        return $loginErrorMsg;
    }
}
?>