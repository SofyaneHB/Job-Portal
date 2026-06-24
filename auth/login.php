<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

require "../config/db.php";
require "../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        set_flash("error", "All fields are required");
        redirect("../Public/login.php");
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        set_flash("error", "Email not found");
        redirect("../Public/login.php");
    }

    if (!password_verify($password, $user['password'])) {
        set_flash("error", "Wrong password");
        redirect("../Public/login.php");
    }

    // ================= SESSION =================
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'] ?? $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['company_id'] = null;

    // ================= COMPANY LINK =================
    if ($user['role'] === 'company') {

        $stmt = $pdo->prepare("
            SELECT id 
            FROM companies 
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$user['id']]);

        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($company) {
            $_SESSION['company_id'] = $company['id'];
        } else {
            // create company row if missing (SAFE FIX)
            $stmt = $pdo->prepare("
                INSERT INTO companies (company_name, user_id)
                VALUES (?, ?)
            ");
            $stmt->execute([
                $user['full_name'] . " Company",
                $user['id']
            ]);

            $_SESSION['company_id'] = $pdo->lastInsertId();
        }
    }

    set_flash("success", "Welcome " . $_SESSION['user_name']);

    // ================= REDIRECT =================
    if ($user['role'] === 'company') {
        redirect("../company/dashboard.php");
    }

    if ($user['role'] === 'admin') {
        redirect("../admin/dashboard.php");
    }

    redirect("../candidate/dashboard.php");
}