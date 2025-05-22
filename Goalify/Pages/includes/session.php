<?php
// Author: Phang Shea Wen
// Date: 2025/01/16
// Filename: session.php
// Description: To create user session and user session cookie
//             1) Registered user session and remember-me cookie
//             2) Admin session (no remember-me cookie)
require_once 'userFunction.php';
function createUserSession(string $userId, string $role, PDO $pdo, string $page, bool $cookie) {
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['role'] = $role;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    if ($cookie) {
        if (!checkCookie($pdo)) {
            createCookie($userId, $pdo);
        }
    }
    return true;
    // string path: "/" -> accessible on the entire domain, "/subdirname/" -> accessible only within this sub directory
}

function sessionTimeOut(string $returnPage) {
    $timeout = 60*60;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        header("location: $returnPage");
        exit();
    }
    $_SESSION['last_activity'] = time();
}

function checkCookie(PDO $pdo) {
    if (isset($_COOKIE['rememberMe'])) {
        $cookieData = unserialize($_COOKIE['rememberMe']);
        $tokenId = $cookieData['tokenId'];
        $userId = $cookieData['userId'];
        $tokenStmt = $pdo -> prepare('SELECT HEX(tokenId) AS tokenId, expiryDateTime FROM rememberMeToken WHERE userId = :userId'); // HEX converts binary data to hexadecimal in upper case
        $tokenStmt -> execute([':userId' => $userId]);
        $storedToken = $tokenStmt -> fetch();
        // strtotime -> converts datetime in SQL to Unix Time stamp
        if ($storedToken) {
            if ((strtolower($storedToken['tokenId']) == $tokenId) && (strtotime($storedToken['expiryDateTime']) > time())){
                // var_dump($_COOKIE['rememberMe']);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function createCookie(string $userId, PDO $pdo) {
    // raw binary data 32 bytes (256 bits), 1 byte = 8 bits
    // bin2hex converts to hexadecimal characters, 64 characters, 1 byte = 2 hex char
    $token = random_bytes(32); // store raw binary data in database
    $expiry = time() + (30*60*60*24);
    $expiryDateTime = date('Y-m-d H:i:s', $expiry);  // Current date and time in DATETIME format
    $tokenStmt = $pdo -> prepare('INSERT INTO rememberMeToken(tokenId, userId, createdDateTime, expiryDateTime) VALUES (:tokenid, :userid, :created, :expiry);');
    $tokenStmt -> execute([':tokenid' => $token, ':userid' => $userId, ':created' => date('Y-m-d H:i:s', time()), ':expiry' => $expiryDateTime]);
    $cookieData = ['userId' => $userId, 'tokenId' => bin2hex($token)]; // bind2hex converts binary data to hexadecimal in lowercase
    setcookie("rememberMe", serialize($cookieData), $expiry, "/", "", false, true);
}
?>