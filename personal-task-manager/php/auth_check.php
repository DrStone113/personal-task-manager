<?php
// auth_check.php — Include this in all pages needing authentication
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please login first.");
}
?>
