document.addEventListener("DOMContentLoaded", function () {
  getPriorityColor();
  setInterval(getPriorityColor, 300000); // Check every 5 minutes
});

function getPriorityColor(priority) {
  const colors = {
    High: "#ff6b6b",
    Medium: "#ffd93d",
    Low: "#6c5ce7",
  };
  return colors[priority] || "#2d3436";
}

function filterTasks() {
    const categoryFilter = document.getElementById("categoryFilter").value;
    const priorityFilter = document.getElementById("priorityFilter").value;
    const statusFilter = document.getElementById("statusFilter").value;

    document.querySelectorAll(".task-card").forEach((card) => {
        const matchesCategory = !categoryFilter || card.dataset.category === categoryFilter;
        const matchesPriority = !priorityFilter || card.dataset.priority === priorityFilter;
        const matchesStatus = !statusFilter || card.dataset.status === statusFilter;

        card.style.display = matchesCategory && matchesPriority && matchesStatus ? "block" : "none";
    });
}

function loadPage(page) {
  const xhr = new XMLHttpRequest();
  const currentScrollPosition = window.scrollY; // Lưu vị trí cuộn hiện tại
  xhr.open("GET", "fetch_tasks.php?page=" + page, true);
  xhr.onload = function () {
    if (xhr.status === 200) {
      const parser = new DOMParser();
      const doc = parser.parseFromString(xhr.responseText, "text/html");
      const taskGrid = doc.getElementById("task-grid");
      const pagination = doc.getElementById("pagination");
      window.scrollTo(0, currentScrollPosition);
      if (taskGrid && pagination) {
        document.getElementById("task-grid").innerHTML = taskGrid.innerHTML;
        document.getElementById("pagination").innerHTML = pagination.innerHTML;
      } else {
        console.error("Phản hồi AJAX không chứa #task-grid hoặc #pagination");
        alert("Đã xảy ra lỗi khi tải dữ liệu. Vui lòng thử lại.");
      }
    } else {
      console.error("Yêu cầu AJAX thất bại với mã trạng thái:", xhr.status);
      alert("Đã xảy ra lỗi khi tải trang. Vui lòng thử lại sau.");
    }
  };
  xhr.onerror = function () {
    alert("Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.");
  };
  xhr.send();
  history.pushState({ page: page }, "", "?page=" + page); // Cập nhật URL
}

document.addEventListener("DOMContentLoaded", function () {
  loadPage(1); // Tải trang đầu tiên khi trang được tải
});

document.getElementById('task-grid').addEventListener('change', function(event) {
    if (event.target.classList.contains('status-select')) {
        const taskId = event.target.dataset.taskId;
        const newStatus = event.target.value;
        updateTaskStatus(taskId, newStatus, event.target);
    }
});

function updateTaskStatus(taskId, newStatus, selectElement) {
    const taskCard = selectElement.closest('.task-card');
    if (!taskCard) {
        console.error('Không tìm thấy task-card');
        return;
    }

    fetch('./php/update_task_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${encodeURIComponent(taskId)}&status=${encodeURIComponent(newStatus)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Phản hồi mạng không thành công');
        }
        return response.text();
    })
    .then(data => {
        if (data.trim() === 'Success') {
            console.log('Trạng thái đã được cập nhật thành công');
            taskCard.dataset.status = newStatus;
            filterTasks(); // Gọi hàm để cập nhật lại giao diện
        } else {
            console.error('Cập nhật trạng thái thất bại:', data);
            alert('Không thể cập nhật trạng thái công việc. Vui lòng thử lại.');
            selectElement.value = taskCard.dataset.status; // Khôi phục giá trị cũ
        }
    })
    .catch(error => {
        console.error('Lỗi khi gửi yêu cầu:', error);
        alert('Đã xảy ra lỗi khi cập nhật trạng thái. Vui lòng thử lại.');
        selectElement.value = taskCard.dataset.status; // Khôi phục giá trị cũ
    });
}

function markTaskAsCompleted(taskId) {
    fetch('php/mark_completed.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${encodeURIComponent(taskId)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Phản hồi mạng không ổn định');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Tìm task card theo taskId
            const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
            if (taskCard) {
                // Ẩn nút "Complete"
                const completeButton = taskCard.querySelector('.btn-complete');
                if (completeButton) {
                    completeButton.style.display = 'none';
                }

                // Cập nhật trạng thái trong dataset
                taskCard.dataset.status = 'Completed';

                // Tìm phần tử cha chứa trạng thái (task-meta)
                const statusContainer = taskCard.querySelector('.task-meta');
                if (statusContainer) {
                    // Xóa dropdown <select> nếu có
                    const selectElement = statusContainer.querySelector('.status-select');
                    if (selectElement) {
                        selectElement.remove();
                    }

                    // Thêm hoặc cập nhật span hiển thị "Completed"
                    let statusElement = statusContainer.querySelector('.status');
                    if (!statusElement) {
                        statusElement = document.createElement('span');
                        statusElement.className = 'status';
                        // Chèn span sau priority để giữ đúng thứ tự
                        const priorityElement = statusContainer.querySelector('.priority');
                        if (priorityElement) {
                            priorityElement.insertAdjacentElement('afterend', statusElement);
                        } else {
                            statusContainer.appendChild(statusElement);
                        }
                    }
                    statusElement.textContent = 'Completed';
                }

                // Gọi filterTasks để áp dụng bộ lọc nếu cần
                filterTasks();
            }
        } else {
            alert('Không thể đánh dấu công việc hoàn thành: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Lỗi khi gọi AJAX:', error);
        alert('Đã xảy ra lỗi khi xử lý yêu cầu. Vui lòng thử lại.');
    });
}