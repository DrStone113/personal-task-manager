<?php
include './php/connect.php';
include './php/auth_check.php'; // Ensure user is logged in

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ./dashboard.php");
    exit();
} else {
    header("Location: ./dashboard.php");
    exit();
}
?>
