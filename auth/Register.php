<?php

require "../config/db.php";
require "../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = clean_input($_POST["fullname"]);
    $email = clean_input($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = $_POST["role"] ?? 'candidate';

    if (!$fullname || !$email || !$password) {
        set_flash("error", "All fields required");
        redirect("../Public/register.php");
    }

    if ($password !== $confirm_password) {
        set_flash("error", "Passwords do not match");
        redirect("../Public/register.php");
    }

    // CHECK EMAIL EXISTS
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        set_flash("error", "Email already exists");
        redirect("../Public/register.php");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // INSERT USER
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password, role)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $fullname,
        $email,
        $hashed_password,
        $role
    ]);

    $user_id = $pdo->lastInsertId();

    // IF COMPANY → CREATE COMPANY ROW
    if ($role === 'company') {

        $stmt = $pdo->prepare("
            INSERT INTO companies (company_name, user_id)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $fullname . " Company",
            $user_id
        ]);
    }

    set_flash("success", "Account created successfully");

    redirect("../Public/login.php");
}