<?php
// Get the current file name to highlight the active menu item
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar">
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
            <ul class="menu-links">
                <li class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                    <a href="./dashboard.php" <?= $current_page === 'dashboard.php' ? 'onclick="return false"' : '' ?>>
                        <i class='bx bx-home-alt icon'></i>
                        <span class="text nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-link <?= $current_page === 'notifications.php' ? 'active' : '' ?>">
                    <a href="./notifications.php" <?= $current_page === 'notifications.php' ? 'onclick="return false"' : '' ?>>
                        <i class='bx bx-bell icon'></i>
                        <span class="text nav-text">Notifications</span>
                    </a>
                </li>
                <li class="nav-link <?= $current_page === 'manage_categories.php' ? 'active' : '' ?>">
                    <a href="manage_categories.php" <?= $current_page === 'manage_categories.php' ? 'onclick="return false"' : '' ?>>
                        <i class='bx bx-category-alt icon'></i>
                        <span class="text nav-text">Categories</span>
                    </a>
                </li>
                <li class="nav-link <?= $current_page === 'admin_console.php' ? 'active' : '' ?>">
                    <a href="admin_console.php">
                        <i class='bx bx-cog icon'></i>
                        <span class="text nav-text">Admin Console</span>
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
<script>
    const sidebar = document.querySelector(".sidebar");
    const sidebarState = localStorage.getItem("sidebarState");
    if (sidebarState === "close") {
        sidebar.classList.add("close");
    } else {
        sidebar.classList.remove("close");
    }
</script>