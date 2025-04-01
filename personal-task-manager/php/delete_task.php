<?php
include 'connect.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$task_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Verify task belongs to user before deleting
$stmt = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Delete task
    $delete_stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $task_id, $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

$stmt->close();
$conn->close();

// Redirect back to dashboard
header("Location: ../dashboard.php");
exit();
?>
