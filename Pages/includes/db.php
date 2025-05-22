<?php
// use pdo (php data objects)
// database secure connection
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=goalify;charset=utf8mb4";
$username = "root";
$pwd = "";

try {
    $pdo = new PDO($dsn, $username, $pwd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed: ". $e -> getMessage());
}
?>