<?php
include 'connect.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch tasks with categories
$stmt = $conn->prepare("
    SELECT t.*, c.category_name, c.color 
    FROM tasks t 
    LEFT JOIN categories c ON t.category_id = c.id 
    WHERE t.user_id = ? 
    ORDER BY t.start_time ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch categories for filter
$cat_stmt = $conn->prepare("SELECT id, category_name FROM categories WHERE user_id = ?");
$cat_stmt->bind_param("i", $user_id);
$cat_stmt->execute();
$categories = $cat_stmt->get_result();
?>
