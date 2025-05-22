<?php
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/db.php';
require $_SERVER['DOCUMENT_ROOT'].'/Goalify/Pages/includes/userFunction.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete-user-id'])) {
        $userId = $_POST['delete-user-id'];
        $userName = getUserName($pdo, $userId);
        if (deleteUser($pdo, $userId)) {
            echo json_encode(['delete' => true, 'msg' => "User '$userName' is deleted!"]);
        } else {
            echo json_encode(['delete' => false, 'msg' => "User '$userName' is not deleted!"]);
        }
    }
}
?>