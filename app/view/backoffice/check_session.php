<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../frontoffice/sign-in.php");
    exit();
}

// Check if user has admin role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // User is logged in but not an admin - redirect to frontoffice
    header("Location: ../frontoffice/profile.php");
    exit();
}
?>
