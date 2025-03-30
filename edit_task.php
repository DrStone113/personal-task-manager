<?php
include './php/connect.php';
include './php/auth_check.php'; // Ensure user is logged in

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $tags = $_POST['tags'];

    $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, priority=?, deadline=?, tags=? WHERE id=?");
    $stmt->bind_param("sssssi", $title, $description, $priority, $deadline, $tags, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ./dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT title, description, priority, deadline, tags FROM tasks WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <title>Edit Task</title>
</head>
<body>
    <h2>Edit Task</h2>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label for="title">Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
        <label for="description">Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($task['description']); ?></textarea>
        <label for="priority">Priority:</label>
        <select name="priority" required>
            <option value="Low" <?php echo $task['priority'] == 'Low' ? 'selected' : ''; ?>>Low</option>
            <option value="Medium" <?php echo $task['priority'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
            <option value="High" <?php echo $task['priority'] == 'High' ? 'selected' : ''; ?>>High</option>
        </select>
        <label for="deadline">Deadline:</label>
        <input type="date" name="deadline" value="<?php echo $task['deadline']; ?>" required>
        <label for="tags">Tags:</label>
        <input type="text" name="tags" value="<?php echo htmlspecialchars($task['tags']); ?>">
        <button type="submit">Update Task</button>
    </form>
</body>
</html>
