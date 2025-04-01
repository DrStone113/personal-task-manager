# Personal Task Manager

A comprehensive task management application for organizing personal and professional tasks.

## Features

- Task creation, editing, and deletion
- Category management with color coding
- Priority levels and status tracking
- Calendar and Gantt chart views
- Deadline notifications
- Task duration tracking
- Dashboard with filtering options

## Technical Structure

The project follows a clear separation of concerns:

- **PHP Files**: Backend logic for database operations and processing
- **CSS Files**: Styling and layout definitions
- **JavaScript Files**: Client-side interactivity and UI enhancements
- **SQL**: Database structure and initial data

### Key Files

- `php/`: Contains all server-side logic
  - `connect.php`: Database connection
  - `auth_check.php`: Authentication verification
  - `*_func.php`: Logic for specific pages

- `css/`: Contains all stylesheets
  - `dashboard.css`: Main layout styling
  - `task_form.css`: Form-specific styles
  - `dashboard_components.css`: Component styling

- `js/`: Contains all JavaScript files
  - `dashboard.js`: Main dashboard functionality
  - `notifications.js`: Notification handling
  - `dashboard_functions.js`: Helper functions

- `sql/`: Database definition
  - `combined_project.sql`: Complete database schema and initial data

## Installation

1. Import `sql/combined_project.sql` into your MySQL database
2. Configure database connection in `php/connect.php`
3. Access the application through your web server

## Default Login

Username: test_user
Password: password
