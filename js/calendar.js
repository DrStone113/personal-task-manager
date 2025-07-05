document.addEventListener('DOMContentLoaded', function() {
    // Calendar elements
    const monthElement = document.querySelector('.month .date');
    const prevBtn = document.querySelector('.month .prev');
    const nextBtn = document.querySelector('.month .next');
    const daysContainer = document.querySelector('.days');
    const todayDateElement = document.querySelector('.today-date .event-date');
    const eventDayElement = document.querySelector('.today-date .event-day');
    const eventsContainer = document.querySelector('.events-list');
    const addEventBtn = document.querySelector('.add-event');
    const addEventWrapper = document.querySelector('.add-event-wrapper');
    const addEventCloseBtn = document.querySelector('.add-event-header .close');
    const addEventSubmitBtn = document.querySelector('.add-event-btn');
    const gotoDateInput = document.querySelector('.date-input');
    const gotoBtn = document.querySelector('.goto-btn');
    const todayBtn = document.querySelector('.today-btn');

    // Current date
    let today = new Date();
    let selectedDate = new Date();
    let month = selectedDate.getMonth();
    let year = selectedDate.getFullYear();

    // Initialize calendar
    initCalendar();

    // Event listeners
    prevBtn.addEventListener('click', () => {
        month--;
        if (month < 0) {
            month = 11;
            year--;
        }
        initCalendar();
    });

    nextBtn.addEventListener('click', () => {
        month++;
        if (month > 11) {
            month = 0;
            year++;
        }
        initCalendar();
    });

    todayBtn.addEventListener('click', () => {
        selectedDate = new Date();
        month = selectedDate.getMonth();
        year = selectedDate.getFullYear();
        initCalendar();
        showEvents(selectedDate);
    });

    gotoBtn.addEventListener('click', () => {
        const dateString = gotoDateInput.value;
        if (dateString) {
            const [inputMonth, inputYear] = dateString.split('/').map(Number);
            if (inputMonth >= 1 && inputMonth <= 12 && inputYear > 0) {
                month = inputMonth - 1;
                year = inputYear;
                initCalendar();
            }
        }
    });

    // Initialize calendar
    function initCalendar() {
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        monthElement.textContent = `${months[month]} ${year}`;
    
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
    
        daysContainer.innerHTML = '';
    
        // Previous month days
        for (let i = firstDay; i > 0; i--) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('prev-date');
            dayElement.textContent = daysInPrevMonth - i + 1;
            daysContainer.appendChild(dayElement);
        }
    
        // Current month days
        for (let i = 1; i <= daysInMonth; i++) {
            const dayElement = document.createElement('div');
            dayElement.textContent = i;
    
            // Thêm lớp selected cho ngày được chọn
            if (i === selectedDate.getDate() && month === selectedDate.getMonth() && year === selectedDate.getFullYear()) {
                dayElement.classList.add('selected');
            }
    
            // Xử lý sự kiện nhấp chuột
            dayElement.addEventListener('click', () => {
                // Xóa lớp selected khỏi ngày trước đó
                const previousSelected = document.querySelector('.days div.selected');
                if (previousSelected) {
                    previousSelected.classList.remove('selected');
                }
                // Thêm lớp selected cho ngày mới
                dayElement.classList.add('selected');
                selectedDate = new Date(year, month, i);
                showEvents(selectedDate);
            });
    
            daysContainer.appendChild(dayElement);
        }
    
        // Next month days
        const totalDays = firstDay + daysInMonth;
        const nextDays = totalDays > 35 ? 42 - totalDays : 35 - totalDays;
        for (let i = 1; i <= nextDays; i++) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('next-date');
            dayElement.textContent = i;
            daysContainer.appendChild(dayElement);
        }
    
        // Fetch tasks để đánh dấu ngày có sự kiện (giữ nguyên logic)
        fetch(`./php/calendar_api.php?action=get_dates_with_tasks&year=${year}&month=${month + 1}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(text => (text.trim() ? JSON.parse(text) : []))
            .then(daysWithTasks => {
                const dayElements = daysContainer.querySelectorAll('div:not(.prev-date):not(.next-date)');
                dayElements.forEach((dayElement, index) => {
                    if (daysWithTasks.includes(index + 1)) {
                        dayElement.classList.add('event');
                    }
                });
            })
            .catch(error => console.error('Error fetching days with tasks:', error));
    
        showEvents(selectedDate);
        updateMonthlyTasks(year, month);
    }

    // Show events for selected date
    function showEvents(date) {
        const dateStr = formatDate(date);
    
        fetch(`./php/calendar_api.php?action=get_tasks_for_date&date=${dateStr}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(text => (text.trim() ? JSON.parse(text) : []))
            .then(tasks => {
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
                eventDayElement.textContent = days[date.getDay()].toLowerCase();
                todayDateElement.textContent = `${date.getDate()}${getOrdinalSuffix(date.getDate())} ${months[date.getMonth()].toLowerCase()} ${year}`;
    
                eventsContainer.innerHTML = '';
                if (tasks.length > 0) {
                    tasks.forEach(task => {
                        const eventElement = document.createElement('div');
                        eventElement.classList.add('event');
                        eventElement.innerHTML = `
                            <div class="title">${task.title}</div>
                            <div class="event-time">${new Date(task.start_time).toLocaleTimeString()} - ${new Date(task.end_time).toLocaleTimeString()}</div>
                            <i class="fas fa-trash delete-event" data-id="${task.id}"></i>
                        `;
                        eventsContainer.appendChild(eventElement);
                    });
    
                    document.querySelectorAll('.delete-event').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const taskId = this.getAttribute('data-id');
                            if (confirm('Are you sure you want to delete this task?')) {
                                fetch(`./php/calendar_api.php?action=delete_task&id=${taskId}`, { method: 'POST' })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            showEvents(selectedDate);
                                            initCalendar();
                                            updateUpcomingTasks();
                                            updateMonthlyTasks(year, month);
                                        }
                                    })
                                    .catch(error => console.error('Error deleting task:', error));
                            }
                        });
                    });
                } else {
                    eventsContainer.innerHTML = '<div class="no-event"><h5>No tasks</h5></div>';
                }
            })
            .catch(error => console.error('Error fetching tasks:', error));
    }

    // Helper functions
    function formatDate(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    function getOrdinalSuffix(day) {
        if (day > 3 && day < 21) return 'th';
        switch (day % 10) {
            case 1: return 'st';
            case 2: return 'nd';
            case 3: return 'rd';
            default: return 'th';
        }
    }
});

function updateUpcomingTasks() {
    fetch('./php/get_upcoming_tasks.php')
        .then(response => response.text())
        .then(html => {
            document.querySelector('.task-grid').innerHTML = html;
        })
        .catch(error => console.error('Error updating upcoming tasks:', error));
}

function updateMonthlyTasks(year, month) {
    fetch(`./php/get_monthly_tasks.php?year=${year}&month=${month + 1}`)
        .then(response => response.text())
        .then(html => {
            document.querySelector('.monthly-tasks .task-list').innerHTML = html;
        })
        .catch(error => console.error('Error updating monthly tasks:', error));
}