-- Add last_notification column to tasks table
ALTER TABLE tasks
ADD COLUMN last_notification DATETIME DEFAULT NULL;

-- Add index for faster notification queries
CREATE INDEX idx_task_notifications ON tasks (user_id, status, start_time, last_notification);

-- Add notification_settings table for user preferences
CREATE TABLE notification_settings (
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
