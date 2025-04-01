<?php
include './php/notifications_func.php'; // Ensure this file is included to fetch settings and upcoming deadlines
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Task Manager</title>
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/notifications.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="./js/dashboard.js?v=<?= time() ?>" defer></script>
    <script src="./js/notifications.js" defer></script>
</head>
<body>
    <?php include './php/sidebar.php'; ?>

    <section class="home"> <!-- Added padding for spacing -->
        <div class="text">Notifications</div>
        
        <div class="container">
            <div class="notification-settings">
                <h3>Notification Settings</h3>
                <form method="POST" action="" class="settings-form">
                    <label>
                        <input type="checkbox" name="notify_before_start_time" 
                            <?= isset($settings) && $settings['notify_before_start_time'] ? 'checked' : '' ?>>
                        Notify before task start_time
                    </label>
                    <label>
                        <input type="checkbox" name="notify_on_assignment" 
                            <?= isset($settings) && $settings['notify_on_assignment'] ? 'checked' : '' ?>>
                        Notify when tasks are assigned
                    </label>
                    <label>
                        <input type="checkbox" name="notify_on_update" 
                            <?= isset($settings) && $settings['notify_on_update'] ? 'checked' : '' ?>>
                        Notify on task updates
                    </label>
                    <label>
                        <input type="checkbox" name="email_notifications" 
                            <?= isset($settings) && $settings['email_notifications'] ? 'checked' : '' ?>>
                        Enable email notifications
                    </label>
                    <button type="submit" name="update_settings" class="btn btn-primary">
                        Save Settings
                    </button>
                </form>
            </div>
        </div>
        <div class="upcoming-deadlines">
            <h3 class="text">Upcoming Deadlines</h3>
            <ul>
                <?php if (isset($upcoming_deadlines)): ?>
                    <?php while ($task = $upcoming_deadlines->fetch_assoc()): ?>
                        <li><?php echo $task['task_name']; ?> - Starting at <?php echo $task['start_time']; ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No upcoming deadlines.</li>
                <?php endif; ?>
            </ul>
        </div>
    </section>
</body>
</html>
