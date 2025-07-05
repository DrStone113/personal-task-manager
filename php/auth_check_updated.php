<?php
// auth_check_updated.php — Include this in all pages needing authentication
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please login first.");
}

// Check if the user role is admin
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header("Location: admin_console.php");
    exit();
}
?>
