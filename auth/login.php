<?php

include "../config/db.php";
require "../includes/functions.php";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = clean_input($_POST['email']);
    $password = ($_POST['password']);

    if(empty($email) || empty($password)) {
        set_flash("error", "All fields are required");
        redirect("../Public/login.php");

    } 
    
    $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
        
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];

        redirect("../Public/index.php");

        } else {
            set_flash("error", "Invalid credentials");
            redirect("../Public/login.php");
        }
    }
?>