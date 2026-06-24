<?php

session_start();

require_once "../config/db.php";
require_once "../includes/functions.php";

require_login();

$user_id = $_SESSION['user_id'];

/* =========================
   GET JOB ID
========================= */
$job_id = $_GET['id'] ?? null;

if (!$job_id) {
    $_SESSION['error_message'] = "Invalid job ID!";
    redirect("dashboard.php");
}

/* =========================
   CHECK IF ALREADY APPLIED
========================= */
$stmt = $pdo->prepare("
    SELECT id FROM applications 
    WHERE job_id = ? AND candidate_id = ?
");
$stmt->execute([$job_id, $user_id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['error_message'] = "You already applied for this job!";
    redirect("applications.php");
}

/* =========================
   INSERT APPLICATION
========================= */
try {

    $stmt = $pdo->prepare("
        INSERT INTO applications (job_id, candidate_id, status, applied_at)
        VALUES (?, ?, 'pending', NOW())
    ");

    $stmt->execute([$job_id, $user_id]);

    $_SESSION['success_message'] = "Application submitted successfully!";
    redirect("applications.php");
    exit;

} catch (PDOException $e) {

    $_SESSION['error_message'] = "Something went wrong!";
    redirect("dashboard.php");
    exit;
}