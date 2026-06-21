<?php

require "../includes/functions.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

logout_user();

session_start();

set_flash('success', 'You have been successfully logged out.');
redirect("../Public/login.php");

?>