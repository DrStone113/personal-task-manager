<?php
include 'connect.php'; // Kết nối database
include 'auth_check.php'; // Kiểm tra đăng nhập

$user_id = $_SESSION['user_id'];
$year = $_GET['year'];
$month = $_GET['month'];

// Lấy tasks trong tháng và năm được chỉ định
$stmt = $conn->prepare("SELECT t.*, c.category_name, c.color FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE t.user_id = ? AND YEAR(t.start_time) = ? AND MONTH(t.start_time) = ? ORDER BY t.start_time ASC");
$stmt->bind_param("iii", $user_id, $year, $month);
$stmt->execute();
$result = $stmt->get_result();

// Render HTML cho tasks của tháng
while ($task = $result->fetch_assoc()) {
    $start_timeDate = new DateTime($task['start_time']);
    $taskDate = $start_timeDate->format('Y-m-d');
    echo "<div class='task' style='background-color: {$task['color']};'>";
    echo "<strong>{$task['title']}</strong><br>";
    echo "Start: {$start_timeDate->format('d M, Y H:i')}<br>";
    echo "Duration: {$task['duration']} mins";
    echo "</div>";
}
$stmt->close();
$conn->close();
?>