<?php
include './php/connect.php';
include './php/auth_check.php';

$user_id = $_SESSION['user_id'];
$tasks_per_page = 6;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $tasks_per_page;

// Cập nhật trạng thái "Overdue" trước khi lấy danh sách công việc
$update_stmt = $conn->prepare("
    UPDATE tasks 
    SET status = 'Overdue' 
    WHERE user_id = ? 
    AND end_time < NOW() 
    AND status != 'Completed'
");
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
$update_stmt->close();

$stmt = $conn->prepare("
    SELECT t.*, c.category_name, c.color 
    FROM tasks t 
    LEFT JOIN categories c ON t.category_id = c.id 
    WHERE t.user_id = ? 
    ORDER BY FIELD(t.status, 'Pending', 'In Progress', 'Overdue', 'Completed'), t.start_time ASC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user_id, $tasks_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Output task list wrapped in task-grid div
echo '<div id="task-grid" class="task-grid">';
while ($task = $result->fetch_assoc()) {
    $start_timeDate = new DateTime($task['start_time']);
    $end_timeDate = new DateTime($task['end_time']);
    $now = new DateTime();
    $interval = $now->diff($start_timeDate);
    $isUrgent = $interval->days <= 2 && $task['status'] !== 'Completed';
?>
    <div class="task-card <?= $isUrgent ? 'urgent' : '' ?>" data-task-id="<?= $task['id'] ?>" data-category="<?= $task['category_id'] ?>" data-priority="<?= $task['priority'] ?>" data-status="<?= $task['status'] ?>">
        <h4><?= htmlspecialchars($task['title']) ?></h4>
        <p class="description"><?= htmlspecialchars($task['description']) ?></p>
        <div class="task-meta">
            <span class="priority <?= strtolower($task['priority']) ?>"><?= $task['priority'] ?></span>
            <?php
            if ($task['status'] == 'Pending' || $task['status'] == 'In Progress') {
                echo '<select class="status-select" data-task-id="' . $task['id'] . '">';
                echo '<option value="Pending"' . ($task['status'] == 'Pending' ? ' selected' : '') . '>Pending</option>';
                echo '<option value="In Progress"' . ($task['status'] == 'In Progress' ? ' selected' : '') . '>In Progress</option>';
                echo '</select>';
            } else {
                echo '<span class="status">' . $task['status'] . '</span>';
            }
            ?>
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
            <?php if ($task['status'] !== 'Completed' && $task['status'] !== 'Overdue'): ?>
                <button class="btn-complete" onclick="markTaskAsCompleted(<?= $task['id'] ?>)">Complete</button>
            <?php endif; ?>
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
echo '</div>';

// Fetch total number of tasks for pagination
$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE user_id = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_tasks = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_tasks / $tasks_per_page);

// Output pagination wrapped in pagination div
echo '<div id="pagination" class="pagination">';
if ($total_pages > 1) {
    if ($current_page > 1) {
        echo '<a href="#" onclick="loadPage(' . ($current_page - 1) . ')" class="page-link">« Previous</a>';
    }
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $current_page) ? 'active' : '';
        echo '<a href="#" onclick="loadPage(' . $i . ')" class="page-link ' . $active . '">' . $i . '</a>';
    }
    if ($current_page < $total_pages) {
        echo '<a href="#" onclick="loadPage(' . ($current_page + 1) . ')" class="page-link">Next »</a>';
    }
}
echo '</div>';

$stmt->close();
$count_stmt->close();
$conn->close();
?>