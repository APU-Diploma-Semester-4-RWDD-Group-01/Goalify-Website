<?php
function getAllUsers($pdo) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM user;');
        $stmt -> execute();
        $users = $stmt -> fetchAll();
        return $users;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting all users: ".$pdoE -> getMessage());
        return false;
    }
}

function searchUserByKeyWord($pdo, $keyword) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM user WHERE `name` LIKE :keyword;');
        $wildcard = "%{$keyword}%";
        $stmt -> execute([':keyword' => $wildcard]);
        $users = $stmt -> fetchAll();
        return $users;
    } catch (PDOException $pdoE) {
        error_log("PDO error searching user: ".$pdoE -> getMessage());
        return false;
    }
}

function getUserById($pdo, $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT * FROM user WHERE `userId` = :userId;');
        $stmt -> execute([':userId' => $userId]);
        $user = $stmt -> fetch();
        if ($user) {
            return $user;
        } else {
            return null;
        }
    } catch (PDOException $pdoE) {
        error_log("PDO error getting user: ".$pdoE -> getMessage());
        return false;
    }
}

function getUserEmail($pdo, string $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT email FROM user WHERE userId = :userId');
        $stmt -> execute([':userId' => $userId]);
        $userId = $stmt -> fetchColumn();
        return $userId;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting user email: ".$pdoE -> getMessage());
        return false;
    }
}

function getUserHashedPwd($pdo, string $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT password FROM user WHERE userId = :userId');
        $stmt -> execute([':userId' => $userId]);
        $pwd = $stmt -> fetchColumn();
        return $pwd;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting user hashed password: ".$pdoE -> getMessage());
        return false;
    }
}

function getUserName($pdo, string $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT name FROM user WHERE userId = :userId');
        $stmt -> execute([':userId' => $userId]);
        $name = $stmt -> fetchColumn();
        return $name;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting user name: ".$pdoE -> getMessage());
        return false;
    }
}

function getUserProfileImg($pdo, string $userId) {
    try {
        $stmt = $pdo -> prepare('SELECT profile_img FROM user WHERE userId = :userId');
        $stmt -> execute([':userId' => $userId]);
        $profile = $stmt -> fetchColumn();
        return $profile;
    } catch (PDOException $pdoE) {
        error_log("PDO error getting user profile image: ".$pdoE -> getMessage());
        return false;
    }
}

function deleteUser($pdo, string $userId) {
    try {
        $stmt = $pdo -> prepare('DELETE FROM `user` WHERE `userId` = :userId');
        $stmt -> execute([':userId' => $userId]);
        return true;
    } catch (PDOException $pdoE) {
        error_log("PDO error deleting user: ".$pdoE -> getMessage());
        return false;
    }
}
?>