<?php

require "../config/db.php";
require "../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = clean_input($_POST["fullname"]);
    $email = clean_input($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $_SESSION['old_fullname'] = $fullname;
    $_SESSION['old_email'] = $email;

    if (strlen($password) < 7) {
        set_flash("error", "Password too weak");
        redirect("../Public/Register.php");
    }

    if ($password !== $confirm_password) {
        set_flash("error", "Passwords do not match");
        redirect("../Public/Register.php");
    }

    if (email_exists($pdo, $email)) {
        set_flash("error", "Email already exists");
        redirect("../Public/Register.php");
    }

    create_user(
        $pdo,
        $fullname,
        $email,
        password_hash($password, PASSWORD_DEFAULT)
    );

    unset($_SESSION['old_fullname'], $_SESSION['old_email']);

    set_flash("success", "Account created successfully");
    redirect("../Public/login.php");
}
?>