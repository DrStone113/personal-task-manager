DROP DATABASE IF EXISTS task_manager;
--
-- Cơ sở dữ liệu: `task_manager`
CREATE DATABASE IF NOT EXISTS task_manager;

USE task_manager;
-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reset_token VARCHAR(100),
    reset_token_expires DATETIME
);

-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_name VARCHAR(50) NOT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#808080',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category_per_user (user_id, category_name)
);

-- Create tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    priority ENUM('Low', 'Medium', 'High') NOT NULL DEFAULT 'Medium',
    status ENUM('Pending', 'In Progress', 'Completed') NOT NULL DEFAULT 'Pending',
    start_time DATETIME NOT NULL,
    duration INT NOT NULL DEFAULT 30, -- Duration in minutes
    tags VARCHAR(255),
    last_notification DATETIME DEFAULT NULL, -- Added from notification_updates.sql
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    notification_type ENUM('immediate', 'upcoming') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_notification (task_id, notification_type)
);

-- Add notification_settings table from notification_updates.sql
CREATE TABLE IF NOT EXISTS notification_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    notify_before_start_time BOOLEAN DEFAULT TRUE,
    notify_on_assignment BOOLEAN DEFAULT TRUE,
    notify_on_update BOOLEAN DEFAULT TRUE,
    email_notifications BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default settings for existing users
INSERT INTO notification_settings (user_id)
SELECT id FROM users
WHERE id NOT IN (SELECT user_id FROM notification_settings);

-- Add indexes for better performance
CREATE INDEX idx_tasks_user ON tasks(user_id);
CREATE INDEX idx_tasks_category ON tasks(category_id);
CREATE INDEX idx_tasks_start_time ON tasks(start_time);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_categories_user ON categories(user_id);
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_notifications_task ON notifications(task_id);
CREATE INDEX idx_notifications_created ON notifications(created_at);
CREATE INDEX idx_task_notifications ON tasks (user_id, status, start_time, last_notification); -- Added from notification_updates.sql

-- Trigger to create default categories for new users
DELIMITER //
CREATE TRIGGER IF NOT EXISTS create_default_categories
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    -- Work-related categories
    INSERT INTO categories (user_id, category_name, color) VALUES
    (NEW.id, 'Work', '#ff6b6b'),
    (NEW.id, 'Meetings', '#4dabf7'),
    (NEW.id, 'Projects', '#845ef7');

    -- Personal categories
    INSERT INTO categories (user_id, category_name, color) VALUES
    (NEW.id, 'Personal', '#51cf66'),
    (NEW.id, 'Health', '#ffd43b'),
    (NEW.id, 'Shopping', '#fd7e14');

    -- Study categories
    INSERT INTO categories (user_id, category_name, color) VALUES
    (NEW.id, 'Study', '#20c997'),
    (NEW.id, 'Homework', '#339af0'),
    (NEW.id, 'Research', '#ae3ec9');
END;
//
DELIMITER ;

-- Trigger to prevent deletion of all categories
DELIMITER //
CREATE TRIGGER IF NOT EXISTS prevent_delete_all_categories
BEFORE DELETE ON categories
FOR EACH ROW
BEGIN
    DECLARE category_count INT;
    
    SELECT COUNT(*) INTO category_count
    FROM categories
    WHERE user_id = OLD.user_id;
    
    IF category_count <= 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete the last remaining category';
    END IF;
END;
//
DELIMITER ;

-- Trigger to clean up old notifications
DELIMITER //
CREATE TRIGGER IF NOT EXISTS cleanup_old_notifications
BEFORE INSERT ON notifications
FOR EACH ROW
BEGIN
    -- Delete notifications older than 7 days
    DELETE FROM notifications 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
END;
//
DELIMITER ;

-- Trigger to delete notifications when task is completed
DELIMITER //
CREATE TRIGGER IF NOT EXISTS delete_completed_task_notifications
AFTER UPDATE ON tasks
FOR EACH ROW
BEGIN
    IF NEW.status = 'Completed' THEN
        DELETE FROM notifications WHERE task_id = NEW.id;
    END IF;
END;
//
DELIMITER ;



-- Trigger to set default settings for existing users
DROP TRIGGER IF EXISTS create_default_notification_settings;
DELIMITER //
CREATE TRIGGER create_default_notification_settings
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO notification_settings (user_id) 
    VALUES (NEW.id);
END;
//
DELIMITER ;

-- Insert test user if not exists
INSERT IGNORE INTO users (username, email, password) 
VALUES ('test_user', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Password is 'password'