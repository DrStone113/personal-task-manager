document.addEventListener('DOMContentLoaded', function() {
    // Initialize Calendar
    initializeCalendarView();
    
    // Initialize Gantt View
    initializeGanttView();

    // Initialize notifications
    checkUpcomingTasks();
    setInterval(checkUpcomingTasks, 300000); // Check every 5 minutes
});

function initializeCalendarView() {
    const calendarEl = document.getElementById('calendar-view');
    if (!calendarEl) return;
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: window.calendarEvents || [],
        eventClick: function(info) {
            // Show task details
            alert(`
                Title: ${info.event.title}
                Description: ${info.event.extendedProps.description}
                Priority: ${info.event.extendedProps.priority}
                Status: ${info.event.extendedProps.status}
                Category: ${info.event.extendedProps.category}
            `);
        }
    });
    calendar.render();
}

function initializeGanttView() {
    const ganttEl = document.getElementById('gantt-view');
    if (!ganttEl) return;
    
    const gantt = new FullCalendar.Calendar(ganttEl, {
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        plugins: ['resourceTimeline'],
        initialView: 'resourceTimelineMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
        },
        aspectRatio: 1.5,
        resourceAreaWidth: '15%',
        slotMinWidth: 100,
        resourceAreaHeaderContent: 'Priority Levels',
        resourceGroupField: 'priority',
        resources: [
            { id: 'high', title: 'High Priority', priority: 'high' },
            { id: 'medium', title: 'Medium Priority', priority: 'medium' },
            { id: 'low', title: 'Low Priority', priority: 'low' }
        ],
        slotDuration: { days: 1 },
        slotLabelFormat: {
            month: 'long',
            day: 'numeric',
            weekday: 'short'
        },
        eventClick: function(info) {
            alert(`
                Title: ${info.event.title}
                Start: ${new Date(info.event.start).toLocaleString()}
                End: ${new Date(info.event.end).toLocaleString()}
                Priority: ${info.event.resourceId.charAt(0).toUpperCase() + info.event.resourceId.slice(1)}
            `);
        },
        eventDidMount: function(info) {
            info.el.style.cursor = 'pointer';
            info.el.style.borderRadius = '4px';
            info.el.style.padding = '2px 6px';
        },
        events: window.ganttEvents || []
    });
    gantt.render();
}

function getPriorityColor(priority) {
    const colors = {
        'High': '#ff6b6b',
        'Medium': '#ffd93d',
        'Low': '#6c5ce7'
    };
    return colors[priority] || '#2d3436';
}

function switchView(view) {
    document.querySelectorAll('.view').forEach(el => el.classList.remove('active'));
    document.getElementById(`${view}-view`).classList.add('active');
}

function filterTasks() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;

    document.querySelectorAll('.task-card').forEach(card => {
        const matchesCategory = !categoryFilter || card.dataset.category === categoryFilter;
        const matchesPriority = !priorityFilter || card.dataset.priority === priorityFilter;
        const matchesStatus = !statusFilter || card.dataset.status === statusFilter;

        card.style.display = (matchesCategory && matchesPriority && matchesStatus) ? 'block' : 'none';
    });
}

function showTodayTasks() {
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('.task-card').forEach(card => {
        const taskDate = card.querySelector('.bx-calendar').nextSibling.textContent.trim();
        const taskDateTime = new Date(taskDate);
        const taskDateStr = taskDateTime.toISOString().split('T')[0];
        
        card.style.display = (taskDateStr === today) ? 'block' : 'none';
    });

    // Switch to task list view if in calendar/gantt view
    document.querySelectorAll('.view').forEach(el => el.classList.remove('active'));
    document.getElementById('task-list').scrollIntoView({ behavior: 'smooth' });

    // Update active state in menu
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    document.querySelector('.nav-link a[onclick*="showTodayTasks"]').parentElement.classList.add('active');
}
