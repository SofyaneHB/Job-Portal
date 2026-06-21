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
function logout_user() {
    session_unset();
    session_destroy();
}

function redirect ($url){
    header("Location: $url");
    exit();
}


function require_login() {
    if (!is_logged_in()){
        set_flash('warning', 'You need to Login');
        redirect("../Public/login.php");
    }
}

function require_guest() {
    if(is_logged_in()) {
        set_flash("info", "You are already logged in");
        redirect('../Public/login.php');
    }
}

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

function display_flash(): void {
    $flash = get_flash();

    if ($flash) {
        $colors = [
            'success' => 'bg-green-500',
            'warning' => 'bg-yellow-500',
            'info'    => 'bg-blue-500',
            'error'   => 'bg-red-500'
        ];

        $colorClass = $colors[$flash['type']] ?? 'bg-red-500';

        echo '<div class="p-3 mb-4 rounded text-white ' . $colorClass . '">';
        echo htmlspecialchars($flash['message']);
        echo '</div>';
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


?>