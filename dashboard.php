<?php
include './php/connect.php';
include './php/auth_check.php'; // Ensure user is logged in

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch tasks
$stmt = $conn->prepare("SELECT id, title, description, priority, deadline, tags FROM tasks WHERE user_id = ? ORDER BY deadline ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- css -->
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <!-- javascript -->
    <script src="./js/dashboard.js" defer></script>
    <!-- boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Dashboard Sidebar Menu | Dark-Light Mode</title>
    <!-- calendar -->
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/main.min.js'></script>
</head>

<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="./img/logo.png" alt="logo">
                </span>

                <div class="text header-text">
                    <span class="name">Planex</span>
                    <span class="profession">Plan your next</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <li class="search-box">
                    <i class='bx bx-search icon'></i>
                    <input type="search" placeholder="Search">
                </li>
                <ul class="menu-links">
                    <li class="nav-link active">
                        <a href="./dashboard.php" onclick="return false">
                            <i class='bx bx-home-alt icon'></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">Notifications</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-task icon'></i>
                            <span class="text nav-text">Today</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-calendar icon'></i>
                            <span class="text nav-text">Upcoming</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-category-alt icon'></i>
                            <span class="text nav-text">Category</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bottom-content">
                <li class="">
                    <a href="./php/logout.php">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>
    <section class="home">
        <div class="text">Dashboard</div>
        <div class="task-container">
            <h2>Your Tasks 📝</h2>
            <div id="calendar"></div>

            <!-- Task Dropdown -->
            <div class="dropdown">
                <select id="categoryDropdown">
                    <option value="" disabled selected>Select Category</option>
                </select>
                <a href="./add_task.php" class="btn">+ Add New Task</a>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                events: [
                    <?php
                    // Reset result pointer
                    $result->data_seek(0);
                    while ($task = $result->fetch_assoc()):
                        $deadline = new DateTime($task['deadline']);
                    ?> {
                            title: '<?= htmlspecialchars($task['title']) ?>',
                            start: '<?= $deadline->format('Y-m-d') ?>',
                            extendedProps: {
                                description: '<?= htmlspecialchars($task['description']) ?>',
                                priority: '<?= htmlspecialchars($task['priority']) ?>',
                                tags: '<?= htmlspecialchars($task['tags']) ?>'
                            },
                            color: getPriorityColor('<?= $task['priority'] ?>')
                        },
                    <?php endwhile; ?>
                ],
                eventDidMount: function(info) {
                    // Thêm tooltip
                    new bootstrap.Tooltip(info.el, {
                        title: `<strong>${info.event.title}</strong><br>
                       ${info.event.extendedProps.description}<br>
                       Priority: ${info.event.extendedProps.priority}<br>
                       Tags: ${info.event.extendedProps.tags}`,
                        placement: 'auto',
                        html: true
                    });
                }
            });

            calendar.render();

            function getPriorityColor(priority) {
                const colors = {
                    high: '#ff6b6b',
                    medium: '#ffd93d',
                    low: '#6c5ce7'
                };
                return colors[priority.toLowerCase()] || '#2d3436';
            }
        });
    </script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>