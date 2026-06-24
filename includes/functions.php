<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function clean_input($data){
    return htmlspecialchars(trim($data));
}

function redirect($url){
    header("Location: $url");
    exit();
}

function logout_user() {
    session_unset();
    session_destroy();
}

/* ================= FLASH ================= */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function display_flash() {
    $flash = get_flash();

    if ($flash) {
        $colors = [
            'success' => 'bg-green-500',
            'warning' => 'bg-yellow-500',
            'info'    => 'bg-blue-500',
            'error'   => 'bg-red-500'
        ];

        $color = $colors[$flash['type']] ?? 'bg-red-500';

        echo "<div class='p-3 mb-4 rounded text-white $color'>";
        echo htmlspecialchars($flash['message']);
        echo "</div>";
    }
}

/* ================= AUTH ================= */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        set_flash("warning", "You need to login first");
        redirect("../Public/login.php");
    }
}

function require_guest() {
    if (is_logged_in()) {
        redirect("../candidate/dashboard.php");
    }
}

function email_exists($pdo, $email) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->rowCount() > 0;
}

function create_user($pdo, $fullname, $email, $password) {
    $stmt = $pdo->prepare("
        INSERT INTO users(full_name, email, password)
        VALUES (?, ?, ?)
    ");

    return $stmt->execute([$fullname, $email, $password]);
}