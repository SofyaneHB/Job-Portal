<?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "JobPortal";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch (PDOException $e) {
    die("Commecction Failed:" . $e->getMessage());
}


?>