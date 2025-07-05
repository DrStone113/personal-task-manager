<?php
include 'connect.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$tasks_per_page = 6; // Number of tasks per page
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Get current page from GET parameter
$offset = ($current_page - 1) * $tasks_per_page; // Calculate offset

// Fetch tasks with categories and implement pagination
$stmt = $conn->prepare("
    SELECT t.*, c.category_name, c.color 
    FROM tasks t 
    LEFT JOIN categories c ON t.category_id = c.id 
    WHERE t.user_id = ? 
    ORDER BY t.start_time ASC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user_id, $tasks_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch categories for filter
$cat_stmt = $conn->prepare("SELECT id, category_name FROM categories WHERE user_id = ?");
$cat_stmt->bind_param("i", $user_id);
$cat_stmt->execute();
$categories = $cat_stmt->get_result();
?>
