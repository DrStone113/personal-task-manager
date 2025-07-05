<?php
include '../php/connect.php';
include '../php/auth_check.php';

if (isset($_POST['id']) && isset($_POST['status'])) {
    $task_id = $_POST['id'];
    $new_status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    // Chỉ cho phép cập nhật nếu trạng thái là "Pending" hoặc "In Progress"
    if (in_array($new_status, ['Pending', 'In Progress'])) {
        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_status, $task_id, $user_id);
        if ($stmt->execute()) {
            echo "Success";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Invalid status";
    }
} else {
    echo "Invalid request";
}
$conn->close();
