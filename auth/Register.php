<?php

session_start();
require "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = $_POST["confirm_password"];

    $_SESSION['old_fullname'] = $fullname;
    $_SESSION['old_email'] = $email;


    if (strlen($password < 7)) {

        $_SESSION['error'] = "The password must contain at least 6 chars";
        $_SESSION['error_field'] = "password";

        header("Location: ../Public/Register.php?error=password_weak");
        exit();
    }


    if ($password != $_POST["confirm_password"]){

        $_SESSION['error'] = "Passwords do not match";
        $_SESSION['error_field'] = "confirm_password";

        header("Location: ../Public/Register.php?error=password_dontmatch");
        exit();
    }

    $check_email = $pdo->prepare("SELECT id FROM users WHERE email = :email");

    $check_email->execute([
        ":email" => $email
    ]);

    if ($check_email->rowCount() > 0 ) {
        
        $_SESSION['error'] = "Email already exists";
        $_SESSION['error_field'] = "email";

        header("Location: ../Public/Register.php?error=email_exists");
        exit();
    }


    try {
        $hashpassword = password_hash($password,PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(full_name, email, password) VALUES (:full_name, :email, :password)";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":full_name" => $fullname,
            ":email" => $email,
            ":password" => $hashpassword
        ]);

        unset($_SESSION['old_fullname'], $_SESSION['old_email']);

        header("Location: ../Public/login.php");
        exit();
    }

    catch (PDOException $e) {
        echo "Invalide " . $e->getMessage();
    }

}

?>