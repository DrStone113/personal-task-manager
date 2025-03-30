<?php
include './php/connect.php';
include './php/auth_check.php'; // Ensure user is logged in

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $deadline = $_POST['deadline'];
    $tags = $_POST['tags'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO tasks (title, description, priority, deadline, tags, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $title, $description, $priority, $deadline, $tags, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ./dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <title>Add Task</title>
</head>
<body>
    <h2>Add New Task</h2>
    <form method="POST" action="">
        <label for="title">Title:</label>
        <input type="text" name="title" required>
        <label for="description">Description:</label>
        <textarea name="description" required></textarea>
        <label for="priority">Priority:</label>
        <select name="priority" required>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>
        <label for="deadline">Deadline:</label>
        <input type="date" name="deadline" required>
        <label for="tags">Tags:</label>
        <input type="text" name="tags">
        <button type="submit">Add Task</button>
    </form>
</body>
</html>
