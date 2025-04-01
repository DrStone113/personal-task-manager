<?php
include './php/connect.php';
include './php/auth_check.php';

$user_id = $_SESSION['user_id'];

// Fetch notification settings
$settings_stmt = $conn->prepare("
    SELECT * FROM notification_settings 
    WHERE user_id = ?
");
$settings_stmt->bind_param("i", $user_id);
$settings_stmt->execute();
$settings = $settings_stmt->get_result()->fetch_assoc();

// Update settings if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_settings'])) {
    $notify_before = isset($_POST['notify_before_start_time']) ? 1 : 0;
    $notify_assignment = isset($_POST['notify_on_assignment']) ? 1 : 0;
    $notify_update = isset($_POST['notify_on_update']) ? 1 : 0;
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;

    $update_stmt = $conn->prepare("
        UPDATE notification_settings 
        SET notify_before_start_time = ?,
            notify_on_assignment = ?,
            notify_on_update = ?,
            email_notifications = ?
        WHERE user_id = ?
    ");
    $update_stmt->bind_param("iiiii", 
        $notify_before, $notify_assignment, 
        $notify_update, $email_notifications, 
        $user_id
    );
    $update_stmt->execute();
    $update_stmt->close();

    // Refresh settings
    $settings_stmt->execute();
    $settings = $settings_stmt->get_result()->fetch_assoc();
}

// Fetch upcoming tasks
$tasks_stmt = $conn->prepare("
    SELECT t.*, c.category_name, c.color 
    FROM tasks t 
    LEFT JOIN categories c ON t.category_id = c.id 
    WHERE t.user_id = ? 
    AND t.status != 'Completed'
    AND t.start_time > NOW()
    ORDER BY t.start_time ASC
    LIMIT 10
");
$tasks_stmt->bind_param("i", $user_id);
$tasks_stmt->execute();
$tasks_stmt->store_result(); // Store the result to free up the connection

// Check for tasks approaching deadlines
$upcoming_deadline_stmt = $conn->prepare("
    SELECT t.*, c.category_name, c.color 
    FROM tasks t 
    LEFT JOIN categories c ON t.category_id = c.id 
    WHERE t.user_id = ? 
    AND t.status != 'Completed'
    AND t.start_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 HOUR)
    ORDER BY t.start_time ASC
");
$upcoming_deadline_stmt->bind_param("i", $user_id);
$upcoming_deadline_stmt->execute();
$upcoming_deadlines = $upcoming_deadline_stmt->get_result();

// Send notifications based on user settings
if ($settings['notify_before_start_time']) {
    while ($task = $upcoming_deadlines->fetch_assoc()) {
        // Logic to send notification (e.g., email or in-app)
        // For example, you could use mail() function for email notifications
        // mail($user_email, "Upcoming Task Reminder", "Your task '{$task['task_name']}' is starting soon.");
    }
}
?>
