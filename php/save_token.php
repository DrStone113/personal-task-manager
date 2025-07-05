<?php
include 'connect.php';
include 'auth_check.php';

$user_id = $_SESSION['user_id'];
$token = $_POST['token'];

$stmt = $conn->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
$stmt->bind_param("si", $token, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();

echo "Token saved successfully";
?>