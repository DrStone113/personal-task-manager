<?php
header('Content-Type: application/json');
include 'connect.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action == 'get_dates_with_tasks') {
    $year = $_GET['year'];
    $month = $_GET['month'];

    // Chuẩn bị truy vấn SQL để lấy các ngày có task trong tháng
    $stmt = $conn->prepare("SELECT DISTINCT DAY(start_time) as day FROM tasks WHERE user_id = ? AND YEAR(start_time) = ? AND MONTH(start_time) = ?");
    $stmt->bind_param("iii", $user_id, $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    // Tạo mảng chứa các ngày có task
    $daysWithTasks = [];
    while ($row = $result->fetch_assoc()) {
        $daysWithTasks[] = (int)$row['day']; // Ép kiểu sang int để đảm bảo dữ liệu sạch
    }

    // Trả về kết quả dưới dạng JSON
    echo json_encode($daysWithTasks);

    $stmt->close();
}elseif ($action == 'add_task_from_calendar') {
    $title = $_POST['title'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $start_time, $end_time);
    $success = $stmt->execute();
    echo json_encode(['status' => $success ? 'success' : 'error']);
    $stmt->close();
} elseif ($action == 'get_tasks_for_date') {
    $date = $_GET['date'];
    $stmt = $conn->prepare("SELECT id, title, start_time, end_time FROM tasks WHERE user_id = ? AND DATE(start_time) = ?");
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
    $stmt->close();
} elseif ($action == 'delete_task') {
    $id = $_GET['id'] ?? null; // Lấy id từ GET thay vì POST
    if ($id === null) {
        echo json_encode(['status' => 'error', 'message' => 'ID is required']);
    } else {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $success = $stmt->execute();
        echo json_encode(['status' => $success ? 'success' : 'error']);
        $stmt->close();
    }
}

$conn->close();
?>