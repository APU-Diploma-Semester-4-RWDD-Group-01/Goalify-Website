<?php
include '../../includes/db.php';
include '../../includes/userHandler.php';
require_once '../../includes/logging.php';

session_start();
$user_id = $_SESSION["user_id"];
if (!isset($_SESSION["user_id"])) {
    $path = "/Goalify/Pages/user/login & register/login.php";
    header("location: $path");
    exit();
}
sessionTimeOut("/Goalify/Pages/user/login & register/login.php");

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_clean();



$repsonse = [];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) || isset($_POST["email"])) {
        $username = isset($_POST["username"]) ? trim($_POST["username"]) : null;
        $email = isset($_POST["email"]) ? trim($_POST["email"]) : null;

        $stmt = $pdo->prepare("SELECT name, email FROM user WHERE userId = :userId");
        $stmt->bindParam(":userId", $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$current_user) {
            echo json_encode(["error" => "User not found"]);
            exit;
        }
        
        if ($username || $email) {
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE (email = :email OR name = :username) AND userId != :userId");
            $check_stmt->bindParam(":email", $email);
            $check_stmt->bindParam(":username", $username);
            $check_stmt->bindParam(":userId", $user_id);
            $check_stmt->execute();            

            if ($check_stmt->fetchColumn() > 0) {
                echo json_encode(["error" => "Username or email already taken"]);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE user SET name = COALESCE(:username, name), email = COALESCE(:email, email) WHERE userId = :userId");
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":userId", $user_id);

            if ($stmt->execute()) {
                if (!empty($username) && $username != $current_user['name']) {
                    insertActivityLog($pdo, $user_id, "A004", "Edited username");
                }
                if (!empty($email) && $email != $current_user['email']) {
                    insertActivityLog($pdo, $user_id, "A005", "Edited email");
                }
                $response["message"] = "Profile updated successfully!";
            } else {
                echo json_encode(["error" => "Failed to update profile"]);
                exit;
            }
        }
    }

    // Update profile picture
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = "uploads/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $profile_img = $target_dir . basename($_FILES["profile_pic"]["name"]);

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_img)) {
            $stmt = $pdo->prepare("UPDATE user SET profile_img = :profile_img WHERE userId = :userId");
            $stmt->bindParam(":profile_img", $profile_img);
            $stmt->bindParam(":userId", $user_id);

            if ($stmt->execute()) {
                $response["profile_img"] = $profile_img;
                insertActivityLog($pdo, $user_id, "A003", "Updated profile image");
            } else {
                echo json_encode(["error" => "Failed to update profile picture"]);
                exit;
            }
        } else {
            echo json_encode(["error" => "File upload failed"]);
            exit;
        }
    }

    // Update password
    if (isset($_POST["new_password"])) {
        $new_password = trim($_POST["new_password"]);
        $confirm_password = trim($_POST["confirm_password"]);

        if ($new_password !== $confirm_password) {
            echo json_encode(["error" => "Passwords do not match"]);
            exit;
        }

        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("UPDATE user SET password = :new_password WHERE userId = :userId");
        $stmt->bindParam(":new_password", $hashed_password);
        $stmt->bindParam(":userId", $user_id);

        if ($stmt->execute()) {
            $response["success"] = true; 
            $response["message"] = "Password updated successfully!";
            insertActivityLog($pdo, $user_id, "A006", "Changed password");
        } else {
            echo json_encode(["error" => "Failed to update password"]);
            exit;
        }
    }
    echo json_encode($response);
    exit;
}


try {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS workspace_count FROM workspace WHERE ownerId = :userId");
    $stmt->bindParam(":userId", $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $workspace_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $workspace_number = $workspace_result ? $workspace_result["workspace_count"] : 0;

    $stmt = $pdo->prepare("SELECT COUNT(p.projectId) AS project_count 
                           FROM workspace w
                           LEFT JOIN project p ON w.workspaceId = p.workspaceId
                           WHERE w.ownerId = :userId");
    
    $stmt->bindParam(":userId", $user_id, PDO::PARAM_STR);
    $stmt->execute();
    $project_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $project_number = $project_result ? $project_result["project_count"] : 0;
    
    $stmt = $pdo->prepare("SELECT * FROM user WHERE userId = :userId");
    $stmt->bindParam(":userId", $user_id, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $user = ["userId" => "", "name" => "", "email" => "", "profile_img" => null];
    } else {
        $user["profile_img"] = $user["profile_img"] ?? null;
    }


    echo json_encode([
        "workspace_count" => $workspace_number,
        "project_count" => $project_number,
        "user" => $user
    ], JSON_UNESCAPED_SLASHES);
    exit;

}catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    exit;
}

?>