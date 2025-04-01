function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count;
            badge.classList.add('show');
        } else {
            badge.textContent = '';
            badge.classList.remove('show');
        }
    }
}

function checkUpcomingTasks() {
    fetch('./php/check_upcoming_tasks.php')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
            if (data.count > 0) {
                showNotifications(data.tasks);
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

function showNotifications(tasks) {
    // Check if browser supports notifications
    if (!("Notification" in window)) {
        console.log("This browser does not support desktop notifications");
        return;
    }

    // Request permission if not granted
    if (Notification.permission !== "granted") {
        Notification.requestPermission();
        return;
    }

    // Show notification for each task
    tasks.forEach(task => {
        const notification = new Notification("Task Reminder", {
            icon: "./img/logo.png",
            body: `${task.title} is due in ${task.time_remaining}\nPriority: ${task.priority}\nCategory: ${task.category}`,
            tag: `task-${task.id}` // Prevent duplicate notifications
        });

        // Close notification after 10 seconds
        setTimeout(() => {
            notification.close();
        }, 10000);

        // Handle notification click
        notification.onclick = function() {
            window.focus();
            window.location.href = `edit_task.php?id=${task.id}`;
        };
    });
}

// Check for notifications when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission
    if ("Notification" in window) {
        if (Notification.permission !== "granted" && Notification.permission !== "denied") {
            Notification.requestPermission();
        }
    }

    // Initial notification check
    checkUpcomingTasks();

    // Check for notifications every minute
    setInterval(checkUpcomingTasks, 60000);
});

function markComplete(taskId) {
    fetch('./php/update_task_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `task_id=${taskId}&status=Completed`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh the page to show updated task list
            window.location.reload();
        } else {
            alert('Error updating task status');
        }
    })
    .catch(error => console.error('Error:', error));
}