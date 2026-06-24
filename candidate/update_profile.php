<?php

session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

require_login();

$user_id = $_SESSION['user_id'];

/* =========================
   GET FORM DATA
========================= */
$full_name = $_POST['full_name'] ?? '';
$phone     = $_POST['phone'] ?? '';
$address   = $_POST['address'] ?? '';
$country   = $_POST['country'] ?? '';
$skills    = isset($_POST['skills']) ? implode(',', $_POST['skills']) : '';

/* =========================
   UPDATE DATABASE
========================= */
try {

    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = ?, phone = ?, address = ?, country = ?, skills = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $full_name,
        $phone,
        $address,
        $country,
        $skills,
        $user_id
    ]);

    /* =========================
       UPDATE SESSION (IMPORTANT)
    ========================= */
    $_SESSION['user_name'] = $full_name;

    /* SUCCESS MESSAGE */
    $_SESSION['success_message'] = "Profile updated successfully!";

    /* REDIRECT */
    redirect("profile.php");
    exit;

} catch (PDOException $e) {

    $_SESSION['error_message'] = "Error updating profile!";
    redirect("profile.php");
    exit;
}