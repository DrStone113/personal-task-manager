<?php
include 'connect.php';
include 'auth_check.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

// Get tasks due within the next 24 hours that haven't been notified recently
$stmt = $conn->prepare("
    SELECT t.*, c.category_name 
    FROM tasks t 
    LEFT JOIN categories c ON t.category_id = c.id 
    WHERE t.user_id = ? 
    AND t.status != 'Completed'
    AND t.start_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
    AND (t.last_notification IS NULL OR t.last_notification < DATE_SUB(NOW(), INTERVAL 1 HOUR))
    ORDER BY t.start_time ASC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$upcoming_tasks = [];
$count = 0;

while ($task = $result->fetch_assoc()) {
    $start_time = new DateTime($task['start_time']);
    $now = new DateTime();
    $interval = $now->diff($start_time);
    
    // Format time remaining
    if ($interval->d > 0) {
        $time_remaining = $interval->format('%d day(s) %h hour(s)');
    } elseif ($interval->h > 0) {
        $time_remaining = $interval->format('%h hour(s) %i minute(s)');
    } else {
        $time_remaining = $interval->format('%i minute(s)');
    }

    $upcoming_tasks[] = [
        'id' => $task['id'],
        'title' => $task['title'],
        'description' => $task['description'],
        'start_time' => $start_time->format('Y-m-d H:i:s'),
        'time_remaining' => $time_remaining,
        'priority' => $task['priority'],
        'category' => $task['category_name'] ?? 'Uncategorized'
    ];
    $count++;

    // Update last notification time
    $update_stmt = $conn->prepare("
        UPDATE tasks 
        SET last_notification = NOW() 
        WHERE id = ? AND user_id = ?
    ");
    $update_stmt->bind_param("ii", $task['id'], $user_id);
    $update_stmt->execute();
    $update_stmt->close();
}

echo json_encode([
    'count' => $count,
    'tasks' => $upcoming_tasks
]);

$stmt->close();
$conn->close();
?>
