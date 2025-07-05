<?php
include 'php/connect.php';
include 'php/auth_check.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Fetch all tasks from all users
if ($conn) {
    $stmt = $conn->prepare("SELECT t.*, c.category_name, c.color, u.username 
        FROM tasks t 
        LEFT JOIN categories c ON t.category_id = c.id 
        LEFT JOIN users u ON t.user_id = u.id 
        ORDER BY t.start_time ASC");
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "Database connection failed.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="./js/dashboard.js?v=<?= time() ?>" defer></script>
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
<?php 
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        include './php/sidebar_admin.php';
    } else {
        include './php/sidebar.php';
    }
    ?>
    <section class="home">
    <div class="text">Admin Panel</div>
    <?php include 'php/manage_users.php'; ?>
    <table>
        <thead>
            <tr>
                <th>Task Title</th>
                <th>Assigned User</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($task = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['username']) ?></td>
                    <td><?= htmlspecialchars($task['category_name']) ?></td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td>
                        <a href="edit_task.php?id=<?= $task['id'] ?>">Edit</a>
                        <a href="php/delete_task.php?id=<?= $task['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </section>
</body>
</html>

<?php
$stmt->close();
?>
