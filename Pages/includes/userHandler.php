<?php
ob_start(); // Start output buffering to prevent extra whitespace
header('Content-Type: application/json');
ob_clean(); // Clear any unwanted output before JSON is returned
require 'db.php';
require('session.php');
require_once 'userFunction.php';
require_once 'logging.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login-email']) && isset($_POST['login-pwd'])) {
        $msg = login($pdo);
        if ($msg === "Login Successfully") {
            echo json_encode(['login' => true, 'msg' => $msg]);
            exit();
        } else {
            echo json_encode(['login' => false, 'msg' => $msg]);
            exit();
        }
    } else if (isset($_POST['register-email']) && isset($_POST['register-pwd'])) {
        $registerName = $_POST['register-name'];
        $registerEmail = $_POST['register-email'];
        $registerPwd = $_POST['register-pwd'];
        $msg = register($pdo, $registerName, $registerEmail, $registerPwd);
        if ($msg === "Registered Successfully") {
            echo json_encode(['register' => true, 'msg' => $msg]);
            exit();
        } else {
            echo json_encode(['register' => false, 'msg' => $msg]);
            exit();
        }
    } else if (isset($_POST['create-user-email']) && isset($_POST['create-user-password'])) {
        $registerName = $_POST['create-user-name'];
        $registerEmail = $_POST['create-user-email'];
        $registerPwd = $_POST['create-user-password'];
        $msg = register($pdo, $registerName, $registerEmail, $registerPwd);
        if ($msg === "Registered Successfully") {
            echo json_encode(['register' => true, 'msg' => $msg]);
            exit();
        } else {
            echo json_encode(['register' => false, 'msg' => $msg]);
            exit();
        }
    } else if (isset($_POST['user-id']) && isset($_POST['modify-user-name']) && isset($_POST['modify-user-email'])) {
        $userId = $_POST['user-id'];
        $oldUserEmail = getUserEmail($pdo, $userId);
        $newUsrName = $_POST['modify-user-name'];
        $newUsrEmail = $_POST['modify-user-email'];
        if ($newUsrName !== '' && $newUsrEmail !== '') {
            if (modifyUserName($pdo, $userId, $newUsrName)) {
                $update = true;
                if ($newUsrEmail !== $oldUserEmail) {
                    $msg = modifyUserEmail($pdo, $userId, $newUsrEmail);
                    if ($msg === "Email Updated Successfully") {
                        $update = true;
                        if (isset($_POST['modify-user-password']) && $_POST['modify-user-password'] !== '') {
                            $newPwd =  $_POST['modify-user-password'];
                            $msg = modifyUserPwd($pdo, $userId, $newPwd);
                            if ($msg === "Password Updated Successfully") {
                                $update = true;
                                $msg = "User is updated!";
                            } else {
                                $update = false;
                            }
                        } else if ($_POST['modify-user-password'] == '') {
                            $update = true;
                            $msg = "User is updated!";
                        }
                    } else {
                        $update = false;
                    }
                } else {
                    if (isset($_POST['modify-user-password']) && $_POST['modify-user-password'] !== '') {
                        $newPwd =  $_POST['modify-user-password'];
                        $msg = modifyUserPwd($pdo, $userId, $newPwd);
                        if ($msg === "Password Updated Successfully") {
                            $update = true;
                            $msg = "User is updated!";
                        } else {
                            $update = false;
                        }
                    } else if ($_POST['modify-user-password'] == '') {
                        $update = true;
                        $msg = "User is updated!";
                    }
                }
                echo json_encode(['update' => $update, 'msg' => $msg, 'userName' => getUserName($pdo, $userId), 'userEmail' => getUserEmail($pdo, $userId)]);
            }
        } else {
            echo json_encode(['update' => false, 'msg' => 'Name and Email cannot be left blank']);
        }
        exit();
    }
}

function login($pdo) {
    $email = trim($_POST['login-email']);
    $userEmail = filter_var($email, FILTER_SANITIZE_EMAIL); // Remove illegal characters
    $userPwd = $_POST['login-pwd'];
    if (isset($_POST['remember-me'])) {
        $rememberMe = $_POST['remember-me'];
    } else {
        $rememberMe = null;
    }
    $stmt = $pdo -> prepare('SELECT * FROM user WHERE email = :email');
    // not necessarily to manually check for SQL statement errors for pdo like in mysqli
    $stmt -> execute([':email' => $userEmail]);
    $user = $stmt -> fetch();
    if ($user && password_verify($userPwd, $user['password'])) {
        createUserSession($user['userId'],'registered-user', $pdo, '/Goalify/Pages/user/todo/task-overview/task-overview.php', isset($rememberMe));
        insertActivityLog($pdo, $user['userId'], 'A001', "Successfully logged in !");
        return "Login Successfully";
    } elseif (!$user) {
        $loginErrorMsg = 'Email not found'; // echo in js, use js to insert error message
        return $loginErrorMsg;
    } elseif (!password_verify($userPwd, $user['password'])) {
        $loginErrorMsg = 'Invalid password';
        return $loginErrorMsg;
    }
}

function register($pdo, $registerName, $registerEmail, $registerPwd) {
    $strongPwd = validatePasswordStrength($registerPwd);
    if ($strongPwd) {
        $hashedPwd = password_hash($registerPwd, PASSWORD_BCRYPT);
        $stmt = $pdo -> prepare('SELECT * FROM user WHERE email = :email');
        $stmt -> execute([':email' => $registerEmail]);
        $result = $stmt -> fetch();
        if ($result) {
            $registerErrorMsg = 'This account is already created';
            return $registerErrorMsg;
        } else {
            $userId = "#USR" . strtolower(substr(md5(uniqid()), 0, 5));
            $stmt = $pdo -> prepare('INSERT INTO user(userId, name, email, password) VALUES (:id, :name, :email, :pwd)');
            $stmt -> execute([':id' => $userId, ':name' => $registerName, ':email' => $registerEmail, ':pwd' => $hashedPwd]);
            return "Registered Successfully";
        }
    } else {
        $registerErrorMsg = 'Weak password';
        return $registerErrorMsg;
    }
}


function modifyUserName($pdo, $userId, $newName) {
    $stmt = $pdo -> prepare('UPDATE `user`
                            SET `name` = :newName
                            WHERE `userId` = :userId');
    $stmt -> execute([':newName' => $newName, ':userId' => $userId]);
    return true;
}

function modifyUserEmail($pdo, $userId, $newEmail) {
    $stmt = $pdo -> prepare('SELECT * FROM user WHERE email = :email');
    $stmt -> execute([':email' => $newEmail]);
    $result = $stmt -> fetch();
    if ($result) {
        $modifyEmailErrorMsg = 'This account is already created';
        return $modifyEmailErrorMsg;
    } else {
        $stmt = $pdo -> prepare('UPDATE `user`
                                SET `email` = :newEmail
                                WHERE `userId` = :userId');
        $stmt -> execute([':newEmail' => $newEmail, ':userId' => $userId]);
        return "Email Updated Successfully";
    }
}

function modifyUserPwd($pdo, $userId, $newPwd) {
    $strongPwd = validatePasswordStrength($newPwd);
    if ($strongPwd) {
        $hashedPwd = password_hash($newPwd, PASSWORD_BCRYPT);
        $stmt = $pdo -> prepare('UPDATE `user`
                                SET `password` = :newPwd
                                WHERE `userId` = :userId');
        $stmt -> execute([':newPwd' => $hashedPwd, ':userId' => $userId]);
        return "Password Updated Successfully";
            // header("location: $page");
    } else {
        $registerErrorMsg = 'Weak password';
        return $registerErrorMsg;
    }
}

function validatePasswordStrength($password) {
    $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&^#()_+])[A-Za-z\d@$!%*?&^#()_+]{8,}$/";

    if (preg_match($pattern, $password)) {
        return true;
    } else {
        return false;
    }
}
?>