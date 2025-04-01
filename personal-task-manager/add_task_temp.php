<?php
include './php/add_task_func.php';

// Fetch the count of existing tasks with the title "New task"
$stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE title LIKE ? AND user_id = ?");
$title_base = 'New task%';
$stmt->bind_param("si", $title_base, $user_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Set the default title based on existing tasks
if ($count > 0) {
    $default_title = "New task (" . ($count + 1) . ")";
} else {
    $default_title = "New task";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/task_form.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Add Task</title>
    <script src="./js/dashboard.js" defer></script>
    <script src="./js/notifications.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const datetimeInput = document.querySelector('input[name="start-time"]');
            const formattedDate = now.toISOString().slice(0, 16);
            datetimeInput.value = formattedDate;
            datetimeInput.min = formattedDate; // Disable past dates
        });
    </script>
</head>
<body>
    <?php include './php/sidebar.php'; ?>

    <section class="home">
        <div class="text">Add New Task</div>
        <div class="container">
        <form method="POST" action="./add_task.php" class="task-form">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" value="<?= htmlspecialchars($default_title) ?>" required class="form-control">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" required class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label for="priority">Priority:</label>
                <select name="priority" required class="form-control">
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" required class="form-control">
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label for="category_id">Category:</label>
                <select name="category_id" class="form-control">
                    <option value="">Select Category</option>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>" style="background: <?= $category['color'] ?>20; color: <?= $category['color'] ?>">
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <a href="manage_categories.php" class="btn btn-secondary" style="margin-top: 10px; display: inline-block;">
                    <i class='bx bx-category'></i> Manage Categories
                </a>
            </div>

            <div class="form-group">
                <label for="start_time">Start time:</label>
                <input type="datetime-local" name="start_time" required class="form-control">
            </div>

            <div class="form-group">
                <label for="duration">Duration (minutes):</label>
                <input type="number" name="duration" value="<?= htmlspecialchars($prefilled_duration) ?>" min="1" required class="form-control">
            </div>

            <div class="form-group">
                <label for="tags">Tags (comma separated):</label>
                <input type="text" name="tags" class="form-control">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Task</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</section>
</body>
</html>
