<?php
include './php/dashboard_func.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planex - Task Manager</title>
    <link rel="icon" type="image/x-icon" href="./img/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/dashboard_components.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/calendar.css?v=<?php echo time(); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- JavaScript -->
    <script src="./js/dashboard.js?v=<?= time() ?>" defer></script>
    <script src="./js/dashboard_func.js?v=<?= time() ?>" defer></script>
    <script src="./js/calendar.js?v=<?= time() ?>" defer></script>
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
        <div class="text">Dashboard</div>

        <!-- Calendar and Events Section -->
        <div class="calendar-container">
            <div class="container">
                <div class="left">
                    <div class="calendar">
                        <div class="month">
                            <i class="fas fa-angle-left prev"></i>
                            <div class="date">december 2015</div>
                            <i class="fas fa-angle-right next"></i>
                        </div>
                        <div class="weekdays">
                            <div>Sun</div>
                            <div>Mon</div>
                            <div>Tue</div>
                            <div>Wed</div>
                            <div>Thu</div>
                            <div>Fri</div>
                            <div>Sat</div>
                        </div>
                        <div class="days">
                            <?php
                            // Loop through the days of the month
                            $startDate = new DateTime('first day of this month');
                            $endDate = new DateTime('last day of this month');
                            $currentDate = clone $startDate;

                            while ($currentDate <= $endDate) {
                                $day = $currentDate->format('d');
                                $month = $currentDate->format('m');
                                $year = $currentDate->format('Y');

                                // Check if there are tasks for this date
                                $tasksForDate = [];
                                $result->data_seek(0); // Reset result pointer
                                while ($task = $result->fetch_assoc()) {
                                    $taskDate = new DateTime($task['start_time']);
                                    if ($taskDate->format('Y-m-d') === $currentDate->format('Y-m-d')) {
                                        $tasksForDate[] = $task;
                                    }
                                }

                                echo "<div class='day' data-date='{$year}-{$month}-{$day}'>";
                                echo "<div class='date'>{$day}</div>";

                                // Display tasks for this date
                                foreach ($tasksForDate as $task) {
                                    echo "<div class='task' style='background-color: {$task['color']};'>{$task['title']}</div>";
                                }

                                echo "</div>"; // Close day div
                                $currentDate->modify('+1 day');
                            }
                            ?>
                        </div>
                        <div class="goto-today">
                            <div class="goto">
                                <input type="text" placeholder="mm/yyyy" class="date-input" />
                                <button class="goto-btn">Go</button>
                            </div>
                            <button class="today-btn">Today</button>
                        </div>
                    </div>
                    <h3>Tasks for the Month</h3>
                    <div class="monthly-tasks">
                        <div class="task-list">
                            <?php
                            $result->data_seek(0);
                            while ($task = $result->fetch_assoc()):
                                $start_timeDate = new DateTime($task['start_time']);
                                $taskDate = $start_timeDate->format('Y-m-d');
                                echo "<div class='task' style='background-color: {$task['color']};'>";
                                echo "<strong>{$task['title']}</strong><br>";
                                echo "Start: {$start_timeDate->format('d M, Y H:i')}<br>";
                                echo "Duration: {$task['duration']} mins";
                                echo "</div>";
                            endwhile;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="right">
                    <div class="today-date">
                        <div class="event-day">wed</div>
                        <div class="event-date">12th december 2022</div>
                    </div>
                    <h3>Tasks for Selected Date:</h3>
                    <div class="daily-events">
                        <div class="events-list"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text">Upcoming Tasks</div>
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

        <div id="task-list">
            <div id="task-grid" class="task-grid">
            </div>
            <div id="pagination" class="pagination">
            </div>
        </div>
        </div>
    </section>
</body>
<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
    import {
        getMessaging,
        getToken
    } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyDZTzEYeJN-pSgypLMFMDzvr6HMrZs61Sk",
        authDomain: "task-manager-c5c37.firebaseapp.com",
        projectId: "task-manager-c5c37",
        storageBucket: "task-manager-c5c37.firebasestorage.app",
        messagingSenderId: "575189565238",
        appId: "1:575189565238:web:910f22e5e1510ca3f63d97",
        measurementId: "G-8B9N1GED18"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    navigator.serviceWorker.register("./js/sw.js?v=<?= time() ?>").then(registration => {
        getToken(messaging, {
            serviceWorkerRegistration: registration,
            vapidKey: 'BD1W3Nt3_QKTQRIVgKxmU82UpdfaQ1gIVMZGvuD5WEt77rXdb20C2juLdrhwPYarY7K8lk23VFlXqPan6NLQoF8'
        }).then((currentToken) => {
            if (currentToken) {
                console.log("Token is: " + currentToken);
                // Gửi token đến server
                fetch('./php/save_token.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'token=' + encodeURIComponent(currentToken),
                    }).then(response => response.text())
                    .then(data => console.log("Server response: " + data))
                    .catch(error => console.error("Error sending token: ", error));
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        }).catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
        });
    });
</script>

</html>

<?php
$stmt->close();
$cat_stmt->close();
$conn->close();
?>