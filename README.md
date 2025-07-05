# Personal Task Manager

A comprehensive web-based application for managing personal tasks with advanced features like notifications, time management, and calendar integration.

## Key Features

### Task Management

- **CRUD Operations**: Create, Read, Update, Delete tasks
- **Task Organization**: Categorize tasks with custom categories
- **Priority System**: Set task priorities (High/Medium/Low)
- **Completion Tracking**: Mark tasks as completed with visual indicators
- **Bulk Operations**: Manage multiple tasks simultaneously

### Time Management

- **Smart Scheduling**: Find optimal time slots for tasks
- **Time Suggestions**: Get recommendations for task scheduling
- **Calendar Integration**: Sync with Google Calendar (coming soon)
- **Deadline Alerts**: Visual indicators for approaching deadlines

### Notifications System

- **Real-time Updates**: Instant notification of task changes
- **Custom Styling**: Visually distinct notification types
- **Status Tracking**: Mark notifications as read/unread
- **Push Notifications**: Browser-based push notifications

### User Management

- **Secure Authentication**: User registration and login
- **Admin Console**: Manage users and system settings
- **Role-based Access**: Different permissions for users/admins
- **Profile Management**: Update user information and preferences

## Installation Guide (for XAMPP on Windows)

### Prerequisites

- **XAMPP**: Download and install from [the official website](https://www.apachefriends.org/index.html). This includes Apache, MySQL, and PHP.
- **Composer**: Download and install from [getcomposer.org](https://getcomposer.org/download/).

### Setup Instructions

**1. Get the Code**

- **Option A: Git (Recommended)**

  1.  Open a terminal or command prompt.
  2.  Navigate to your XAMPP installation's `htdocs` directory:
      ```bash
      cd C:/xampp/htdocs
      ```
  3.  Clone the repository:
      ```bash
      git clone https://github.com/your-repo/personal-task-manager.git
      ```
  4.  Navigate into the project directory:
      ```bash
      cd personal-task-manager
      ```

- **Option B: Manual Download**
  1.  Download the project ZIP file.
  2.  Extract the contents into your XAMPP's `htdocs` directory.
  3.  Rename the extracted folder to `personal-task-manager`.

**2. Install Dependencies**

1.  Open a terminal or command prompt inside the `personal-task-manager` directory.
2.  Run the following command to install the required PHP libraries:
    ```bash
    composer install
    ```

**3. Database Setup**

1.  Start the **Apache** and **MySQL** modules from the XAMPP Control Panel.
2.  Open your web browser and go to `http://localhost/phpmyadmin/`.
3.  Click on the **New** button in the left sidebar to create a new database.
4.  Enter `task_manager` as the database name and click **Create**.
5.  Select the newly created `task_manager` database from the sidebar.
6.  Click on the **Import** tab at the top.
7.  Click **Choose File** and navigate to the project's `sql` folder. Select the `task_manager.sql` file.
8.  Scroll down and click the **Go** button to import the database schema.

**4. Configure the Project**

1.  Open the file `personal-task-manager/php/connect.php` in a code editor.
2.  Verify that the database credentials match your MySQL setup. The default XAMPP settings are usually correct:

    ```php
    <?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "task_manager";

    // Connect to database
    $conn = new mysqli($host, $user, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    ```

**5. Run the Application**

1.  Open your web browser and navigate to:
    ```
    http://localhost/personal-task-manager/
    ```
2.  You should see the application's login/register page.

## Usage

### Basic Operations

1. **Register** a new account
2. **Login** with your credentials
3. **Add tasks** with details like:
   - Title
   - Description
   - Category
   - Priority
   - Due date
4. **Organize tasks** using the dashboard interface
5. **Receive notifications** for important events

### Advanced Features

- Use the time finder to schedule tasks efficiently
- Set up recurring tasks (coming soon)
- Export tasks to CSV/PDF (coming soon)

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please follow the existing code style and add tests for new features.

## License

Distributed under the MIT License.

## Contact

Project Maintainer: Nguyen Khang
Email: khang77955@gmail.com
Project Link: https://github.com/DrStone113/personal-task-manager
