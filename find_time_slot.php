<?php
include './php/find_free_time_func.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planex - Find Free Time Slot</title>
    <link rel="icon" type="image/x-icon" href="./img/logo.png">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/findfreetime.css?php echo time(); ?>">
    <script src="./js/dashboard.js?v=<?= time() ?>" defer></script>
    <script src="./js/start_time.js?v=<?= time() ?>" defer></script>
    <link rel="stylesheet" href="./css/dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
        <div class="text">Find Free Time Slot</div>
        <div class="container">
            <form method="POST" action="" class="search-form">
                <div class="form-group">
                    <label for="duration">Duration (minutes):</label>
                    <input type="number" name="duration" value="<?= $duration ?>" min="15" step="15" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="start_time">Start Date:</label>
                    <input type="datetime-local" name="start_time" value="<?= $start_date ?>" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="datetime-local" name="end_date" value="<?= $end_date ?>" required class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Find Available Slots</button>
            </form>

            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <div class="results">
                    <h3>Available Time Slots</h3>
                    <?php if (empty($available_slots)): ?>
                        <p>No available slots found in the selected date range.</p>
                    <?php else: ?>
                        <div class="slots-grid">
                            <?php foreach ($available_slots as $slot): ?>
                                <div class="slot-card">
                                    <div class="slot-time">
                                        <i class='bx bx-time'></i>
                                        <?= $slot['start']->format('D, M j, Y g:i A') ?> -
                                        <?= $slot['end']->format('g:i A') ?>
                                    </div>
                                    <a href="add_task.php?datetime=<?= urlencode($slot['start']->format('Y-m-d\TH:i')) ?>&duration=<?= $duration ?>"
                                        class="btn btn-secondary">
                                        Schedule Task
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>

</html>

<?php
if (isset($stmt)) $stmt->close();
$conn->close();
?>