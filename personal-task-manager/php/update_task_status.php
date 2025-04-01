<?php
include 'connect.php';
include 'auth_check.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id']) && isset($_POST['status'])) {
    $task_id = intval($_POST['task_id']);
    $status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['Pending', 'In Progress', 'Completed'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid status']);
        exit;
    }

    // Update task status
    $stmt = $conn->prepare("
        UPDATE tasks 
        SET status = ?,
            last_notification = NULL
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("sii", $status, $task_id, $user_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        
        // If task is completed, clear its notifications
        if ($status === 'Completed') {
            $stmt = $conn->prepare("
                UPDATE tasks 
                SET last_notification = NOW()
                WHERE id = ? AND user_id = ?
            ");
            $stmt->bind_param("ii", $task_id, $user_id);
            $stmt->execute();
        }
    } else {
        $response['error'] = 'Database error';
    }
    
    $stmt->close();
} else {
    $response['error'] = 'Invalid request';
}

$conn->close();
echo json_encode($response);
?>
