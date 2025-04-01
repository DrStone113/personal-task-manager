<?php
include './php/edit_task_func.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/task_form.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Edit Task</title>
    <script src="./js/dashboard.js" defer></script>
    <script src="./js/notifications.js" defer></script>
</head>

<body>
    <?php include './php/sidebar.php'; ?>

    <section class="home">
        <div class="text">Edit Task</div>
        <div class="container">
            <form method="POST" action="./edit_task.php?id=<?= $task_id ?>" class="task-form">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" class="form-control"><?= htmlspecialchars($task['description']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="priority">Priority:</label>
                    <select name="priority" required class="form-control">
                        <option value="Low" <?= $task['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= $task['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= $task['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" required class="form-control">
                        <option value="Pending" <?= $task['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Category:</label>
                    <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php
                        $categories->data_seek(0);
                        while ($category = $categories->fetch_assoc()):
                        ?>
                            <option value="<?= $category['id'] ?>"
                                <?= $task['category_id'] == $category['id'] ? 'selected' : '' ?>
                                style="background: <?= $category['color'] ?>20; color: <?= $category['color'] ?>">
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <a href="manage_categories.php" class="btn btn-secondary" style="margin-top: 10px; display: inline-block;">
                        <i class='bx bx-category'></i> Manage Categories
                    </a>
                </div>

                <div class="form-group">
                    <label for="start_time">Start Time:</label>
                    <input type="datetime-local" name="start_time"
                        value="<?= date('Y-m-d\TH:i', strtotime($task['start_time'])) ?>"
                        required class="form-control">
                </div>

                <div class="form-group">
                    <label for="duration">Duration (minutes):</label>
                    <input type="number" name="duration" value="<?= $task['duration'] ?>"
                        min="1" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="tags">Tags (comma separated):</label>
                    <input type="text" name="tags" value="<?= htmlspecialchars($task['tags']) ?>" class="form-control">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</body>

</html>

<?php
$stmt->close();
$cat_stmt->close();
$conn->close();
?>