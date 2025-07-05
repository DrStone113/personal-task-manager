<?php
include 'connect.php'; // Kết nối database
include 'auth_check.php'; // Kiểm tra đăng nhập

$user_id = $_SESSION['user_id'];

// Lấy danh sách tasks sắp tới
$stmt = $conn->prepare("SELECT t.*, c.category_name, c.color FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE t.user_id = ? ORDER BY t.start_time ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Render HTML cho danh sách tasks
while ($task = $result->fetch_assoc()) {
    $start_timeDate = new DateTime($task['start_time']);
    $end_timeDate = new DateTime($task['end_time']);
    $now = new DateTime();
    $interval = $now->diff($start_timeDate);
    $isUrgent = $interval->days <= 2 && $task['status'] !== 'Completed';
    ?>
    <div class="task-card <?= $isUrgent ? 'urgent' : '' ?>" data-category="<?= $task['category_id'] ?>" data-priority="<?= $task['priority'] ?>" data-status="<?= $task['status'] ?>">
        <h4><?= htmlspecialchars($task['title']) ?></h4>
        <p class="description"><?= htmlspecialchars($task['description']) ?></p>
        <div class="task-meta">
            <span class="priority <?= strtolower($task['priority']) ?>"><?= $task['priority'] ?></span>
            <span class="status"><?= $task['status'] ?></span>
            <span class="category" style="background: <?= $task['color'] ?? '#808080' ?>20; color: <?= $task['color'] ?? '#808080' ?>">
                <?= $task['category_name'] ?? 'Uncategorized' ?>
            </span>
        </div>
        <div class="task-time">
            <i class='bx bx-time'></i> <?= $task['duration'] ?> mins
            <br>
            <i class='bx bx-calendar'></i> Start: <?= $start_timeDate->format('d M, Y H:i') ?>
            <br>
            End: <?= $end_timeDate->format('d M, Y H:i') ?>
        </div>
        <div class="task-actions">
            <a href="mark_completed.php?id=<?= $task['id'] ?>" class="btn-complete">
                <i class='bx bx-check'></i> Complete
            </a>
            <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn-edit">
                <i class='bx bx-edit'></i>
            </a>
            <a href="php/delete_task.php?id=<?= $task['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?')">
                <i class='bx bx-trash'></i>
            </a>
        </div>
    </div>
    <?php
}
$stmt->close();
$conn->close();
?>