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

function redirect ($url){
    header("Location: $url");
    exit();
}

function require_login() {
    if (!is_logged_in()){
        $_SESSION['flash'] = [
            'type' => 'Warning',
            'message' => 'You need to Login'
        ];
        redirect('../Public/login.php');
    }
}

function require_guest() {
    if(is_logged_in()) {
        redirect('../candidate/dashboard.php');
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
        $colorClass = $flash['type'] === 'success' 
            ? 'bg-green-500' 
            : 'bg-red-500';

        echo '<div class="p-3 mb-4 rounded text-white ' . $colorClass . '">';
        echo htmlspecialchars($flash['message']);
        echo '</div>';
    }
}


?>