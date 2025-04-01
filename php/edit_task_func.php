<?php
include 'connect.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$task_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch categories for the user
$cat_stmt = $conn->prepare("SELECT id, category_name, color FROM categories WHERE user_id = ?");
$cat_stmt->bind_param("i", $user_id);
$cat_stmt->execute();
$categories = $cat_stmt->get_result();

// Fetch task details
$stmt = $conn->prepare("
    SELECT t.*, c.category_name, c.color 
    FROM tasks t 
    LEFT JOIN categories c ON t.category_id = c.id 
    WHERE t.id = ? AND t.user_id = ?
");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if (!$task) {
    header("Location: ../dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $start_time = $_POST['start_time'];
    $duration = $_POST['duration'];
    $status = $_POST['status'];
    $tags = $_POST['tags'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

    $update_stmt = $conn->prepare("
        UPDATE tasks 
        SET title = ?, description = ?, priority = ?, start_time = ?, 
            duration = ?, status = ?, tags = ?, category_id = ?
        WHERE id = ? AND user_id = ?
    ");
    $update_stmt->bind_param("ssssisisii", 
        $title, $description, $priority, $start_time, 
        $duration, $status, $tags, $category_id, 
        $task_id, $user_id
    );
    $update_stmt->execute();
    $update_stmt->close();

    header("Location: ./dashboard.php");
    exit();
}
?>
