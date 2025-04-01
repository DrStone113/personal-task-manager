<?php
include './php/dashboard_func.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Dashboard</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/dashboard_components.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/gantt.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Calendar & Gantt -->
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/timeline@6.1.8/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timeline@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/timeline@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timeline@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/resource@6.1.8/main.min.js'></script>
    
    <!-- JavaScript -->
    <script src="./js/notifications.js?v=<?= time() ?>" defer></script>
    <script src="./js/dashboard_functions.js?v=<?= time() ?>" defer></script>
    <script src="./js/dashboard.js?v=<?= time() ?>" defer></script>
</head>

<body>
<?php include './php/sidebar.php'; ?>

    <section class="home">
        <div class="text">Dashboard</div>
        
        <div class="controls">
            <div class="filters">
                <select id="categoryFilter" onchange="filterTasks()">
                    <option value="">All Categories</option>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <select id="priorityFilter" onchange="filterTasks()">
                    <option value="">All Priorities</option>
                    <option value="High">High Priority</option>
                    <option value="Medium">Medium Priority</option>
                    <option value="Low">Low Priority</option>
                </select>

                <select id="statusFilter" onchange="filterTasks()">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="action-buttons">
                <a href="find_time_slot.php" class="btn find-slot">Find Free Time Slot</a>
                <a href="add_task.php" class="btn add-task">Add New Task</a>
            </div>
        </div>

        <div id="calendar-view" class="view active"></div>
        <div id="gantt-view" class="view"></div>
        
        <div id="task-list">
            <h3 class="text">Upcoming Tasks</h3>
            <div class="task-grid">
                <?php 
                $result->data_seek(0);
                while ($task = $result->fetch_assoc()): 
                    $start_timeDate = new DateTime($task['start_time']);
                    $now = new DateTime();
                    $interval = $now->diff($start_timeDate);
                    $isUrgent = $interval->days <= 2 && $task['status'] !== 'Completed';
                ?>
                    <div class="task-card <?= $isUrgent ? 'urgent' : '' ?>" data-category="<?= $task['category_id'] ?>" data-priority="<?= $task['priority'] ?>" data-status="<?= $task['status'] ?>">
                        <h4><?= htmlspecialchars($task['title']) ?></h4>
                        <p class="description"><?= htmlspecialchars($task['description']) ?></p>
                        <div class="task-meta">
                            <span class="priority <?= strtolower($task['priority']) ?>"><?= $task['priority'] ?></span>
                            <span class="status"><?= $task['status'] ?></span>
                            <span class="category" style="background: <?= $task['color'] ?? '#808080' ?>20; color: <?= $task['color'] ?? '#808080' ?>">
                                <?= $task['category_name'] ?? 'Uncategorized' ?>
                            </span>
                        </div>
                        <div class="task-time">
                            <i class='bx bx-time'></i> <?= $task['duration'] ?> mins
                            <br>
<i class='bx bx-calendar'></i> <?= $start_timeDate->format('M d, Y H:i') ?>
                        </div>
                        <div class="task-actions">
                            <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn-edit">
                                <i class='bx bx-edit'></i>
                            </a>
                            <a href="php/delete_task.php?id=<?= $task['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure?')">
                                <i class='bx bx-trash'></i>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <script>
        // Initialize calendar and gantt event data
        window.calendarEvents = <?php
            $result->data_seek(0);
            $tasks = [];
            while ($task = $result->fetch_assoc()) {
                $start_time = new DateTime($task['start_time']);
                $endTime = clone $start_time;
                $endTime->modify('+' . $task['duration'] . ' minutes');
                
                $tasks[] = [
                    'id' => $task['id'],
                    'title' => addslashes($task['title']),
                    'start' => $start_time->format('Y-m-d\TH:i:s'),
                    'end' => $endTime->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => "getPriorityColor('{$task['priority']}')",
                    'extendedProps' => [
                        'description' => addslashes($task['description']),
                        'priority' => $task['priority'],
                        'status' => $task['status'],
                        'category' => addslashes($task['category_name'] ?? 'Uncategorized')
                    ]
                ];
            }
            echo json_encode($tasks);
        ?>;

        window.ganttEvents = <?php
            $result->data_seek(0);
            $ganttTasks = [];
            while ($task = $result->fetch_assoc()) {
                $start_time = new DateTime($task['start_time']);
                $endTime = clone $start_time;
                $endTime->modify('+' . $task['duration'] . ' minutes');
                
                $ganttTasks[] = [
                    'id' => $task['id'],
                    'resourceId' => strtolower($task['priority']),
                    'title' => addslashes($task['title']),
                    'start' => $start_time->format('Y-m-d\TH:i:s'),
                    'end' => $endTime->format('Y-m-d\TH:i:s'),
                    'backgroundColor' => "getPriorityColor('{$task['priority']}')"
                ];
            }
            echo json_encode($ganttTasks);
        ?>;
    </script>
</body>
</html>

<?php
$stmt->close();
$cat_stmt->close();
$conn->close();
?>
