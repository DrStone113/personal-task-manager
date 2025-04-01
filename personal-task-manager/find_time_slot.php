<?php
include './php/find_free_time_func.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Free Time Slot</title>
    <script src="./js/start_time.js?v=<?= time() ?>" defer></script>
    <link rel="stylesheet" href="./css/dashboard.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php include './php/sidebar.php'; ?>

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

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .search-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .slot-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .slot-time {
            font-size: 1.1em;
            color: var(--text-color);
        }
        .slot-time i {
            margin-right: 5px;
            color: var(--primary-color);
        }
        .results h3 {
            margin: 20px 0;
            color: var(--text-color);
        }
    </style>

    <script src="./js/dashboard.js"></script>
    <script src="./js/notifications.js"></script>
</body>
</html>

<?php
if (isset($stmt)) $stmt->close();
$conn->close();
?>
