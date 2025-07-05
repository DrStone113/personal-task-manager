DROP DATABASE IF EXISTS task_manager;

-- Cơ sở dữ liệu: `task_manager`
CREATE DATABASE IF NOT EXISTS task_manager;

USE task_manager;
-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'locked') NOT NULL DEFAULT 'active',
    reset_token VARCHAR(100),
    reset_token_expires DATETIME,
    fcm_token VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
    status ENUM('Pending', 'In Progress', 'Completed', 'Overdue') NOT NULL DEFAULT 'Pending',
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    duration INT NOT NULL DEFAULT 30, -- Duration in minutes
    tags VARCHAR(255),
    last_notification DATETIME DEFAULT NULL, -- Added from notification_updates.sql
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Add indexes for better performance
CREATE INDEX idx_tasks_user ON tasks(user_id);
CREATE INDEX idx_tasks_category ON tasks(category_id);
CREATE INDEX idx_tasks_start_time ON tasks(start_time);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_categories_user ON categories(user_id);

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

--Trigger to set end_time of task
DELIMITER //
CREATE TRIGGER update_end_time
BEFORE INSERT ON tasks
FOR EACH ROW
BEGIN
    SET NEW.end_time = DATE_ADD(NEW.start_time, INTERVAL NEW.duration MINUTE);
END;
//
DELIMITER ;

--Trigger to set end_time of task when start_time is updated
DELIMITER //
CREATE TRIGGER update_end_time_on_update
BEFORE UPDATE ON tasks
FOR EACH ROW
BEGIN
    IF NEW.start_time != OLD.start_time OR NEW.duration != OLD.duration THEN
        SET NEW.end_time = DATE_ADD(NEW.start_time, INTERVAL NEW.duration MINUTE);
    END IF;
END;
//
DELIMITER ;

-- Insert test user if not exists
INSERT IGNORE INTO users (username, email, password) 
VALUES ('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Password is 'password'

-- Insert admin account if not exists
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@example.com', '$2y$10$zUsLnsm1C6Ij2jGYOsVqFOpAwBBYrE5xaRR1BLWnbDep5oQkvOKg2', 'admin');
-- Password is 'admin'

INSERT INTO tasks (user_id, category_id, title, priority, status, start_time, duration)
VALUES
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Meetings'),
    'Schedule client call',
    'Medium',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    30
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Shopping'),
    'Buy new laptop accessories',
    'Low',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    60
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Health'),
    'Visit the doctor',
    'High',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    90
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Research'),
    'Analyze competitor research',
    'Medium',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    120
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Homework'),
    'Finish physics assignment',
    'High',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    90
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Personal'),
    'Call family members',
    'Low',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    30
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Projects'),
    'Update project timeline',
    'High',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    60
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Study'),
    'Review course materials',
    'Medium',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    90
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Personal'),
    'Organize desk',
    'Low',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    60
),
(
    (SELECT id FROM users WHERE username='testuser'),
    (SELECT id FROM categories WHERE user_id=(SELECT id FROM users WHERE username='testuser') AND category_name='Work'),
    'Draft weekly report',
    'High',
    'Pending',
    NOW() + INTERVAL (RAND() * 48 * 3600 - 24 * 3600) SECOND,
    120
);