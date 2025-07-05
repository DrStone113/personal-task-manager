<?php
include './php/connect.php'; // Kết nối cơ sở dữ liệu
include './php/auth_check.php'; // Kiểm tra đăng nhập

$user_id = $_SESSION['user_id'];

$update_stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
$update_stmt->close();
// Lấy danh sách thông báo
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY sent_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planex - Notifications</title>
    <link rel="icon" type="image/x-icon" href="./img/logo.png">
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/notifications.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="./js/dashboard.js?v=<?= time() ?>" defer></script>
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
        <div class="text">Notifications</div>
        <div class="notification-list">
            <?php while ($notification = $result->fetch_assoc()): ?>
                <div class="notification-card <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>">
                    <h4><?php echo htmlspecialchars($notification['title']); ?></h4>
                    <p><?php echo htmlspecialchars($notification['body']); ?></p>
                    <span class="sent-time"><?php echo $notification['sent_at']; ?></span>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>