<?php
include 'connect.php';
include 'auth_check.php'; // Ensure user is logged in

$user_id = $_SESSION['user_id'];

// Get pre-filled values from time slot finder
$prefilled_datetime = isset($_GET['datetime']) ? date('Y-m-d\TH:i', strtotime($_GET['datetime'])) : '';
$prefilled_duration = isset($_GET['duration']) ? intval($_GET['duration']) : 60;

// Set default deadline to tomorrow if not prefilled
if (empty($prefilled_datetime)) {
    $tomorrow = new DateTime('tomorrow');
    $prefilled_datetime = $tomorrow->format('Y-m-d\TH:i');
}

// Fetch categories for the user
$stmt = $conn->prepare("SELECT id, category_name, color FROM categories WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$categories = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $start_time = $_POST['start_time'];
    $tags = $_POST['tags'];
    $duration = $_POST['duration'];
    $status = $_POST['status'];
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

    $stmt = $conn->prepare("INSERT INTO tasks (title, description, priority, start_time, tags, duration, status, category_id, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssisii", $title, $description, $priority, $start_time, $tags, $duration, $status, $category_id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: ./dashboard.php");
    exit();
}
?>
