<?php
session_start();
include 'connect.php'; // File kết nối cơ sở dữ liệu

$user_id = $_SESSION['user_id']; // Lấy user_id từ session

// Hàm tìm khoảng thời gian trống (cần được định nghĩa)
function findFreeTimeSlot($conn, $user_id, $duration) {
    $stmt = $conn->prepare("SELECT start_time, duration FROM tasks WHERE user_id = ? ORDER BY start_time");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);

    $current_time = new DateTime();
    $suggested_time = clone $current_time;

    while (true) {
        $end_time = clone $suggested_time;
        $end_time->modify("+$duration minutes");
        $is_free = true;

        foreach ($tasks as $task) {
            $task_start = new DateTime($task['start_time']);
            $task_end = clone $task_start;
            $task_end->modify("+{$task['duration']} minutes");

            if ($suggested_time < $task_end && $end_time > $task_start) {
                $is_free = false;
                $suggested_time = clone $task_end;
                break;
            }
        }

        if ($is_free) {
            return $suggested_time->format('Y-m-d H:i:s');
        }
    }
}

if (isset($_POST['duration'])) {
    $duration = intval($_POST['duration']);
    $suggested_time = findFreeTimeSlot($conn, $user_id, $duration);

    if ($suggested_time) {
        echo json_encode(['suggested_time' => $suggested_time]);
    } else {
        echo json_encode(['error' => 'Không tìm được khoảng thời gian trống']);
    }
} else {
    echo json_encode(['error' => 'Thiếu thời lượng công việc']);
}
?>