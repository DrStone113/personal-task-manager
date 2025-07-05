<?php
// Đặt header để đảm bảo phản hồi luôn là JSON
header('Content-Type: application/json');

// Bật hiển thị lỗi trong quá trình debug (xóa sau khi hoàn tất)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php'; // Đảm bảo đường dẫn đúng
include 'auth_check.php'; // Đảm bảo đường dẫn đúng và session đã được khởi động

// Kiểm tra xem POST['id'] có tồn tại không
if (isset($_POST['id'])) {
    $task_id = $_POST['id'];
    $user_id = $_SESSION['user_id'];

    // Chuẩn bị và thực thi câu lệnh SQL
    $stmt = $conn->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $task_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật trạng thái công việc.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi chuẩn bị câu lệnh SQL.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Không có ID công việc được cung cấp.']);
}

exit; // Đảm bảo không có mã nào khác được thực thi sau khi trả về JSON
?>