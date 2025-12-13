-- SQL script to create admin users table for The Continuum Journal Admin System
-- Run this SQL on your MySQL database: netsutra_journal

-- Create admin_users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT NULL,
    FOREIGN KEY (created_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Create admin_sessions table for session management
CREATE TABLE IF NOT EXISTS admin_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_activity DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
);

-- Create admin_login_attempts table for security
CREATE TABLE IF NOT EXISTS admin_login_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50),
    success BOOLEAN DEFAULT FALSE,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip_address, attempt_time),
    INDEX idx_username_time (username, attempt_time)
);

-- Insert default super admin user (password: admin123 - CHANGE THIS IMMEDIATELY!)
-- Password hash for 'admin123' using PHP password_hash() with PASSWORD_DEFAULT
INSERT INTO admin_users (username, email, password_hash, full_name, role, created_at) 
VALUES (
    'admin', 
    'admin@phindia.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'System Administrator', 
    'super_admin', 
    NOW()
) ON DUPLICATE KEY UPDATE 
    password_hash = VALUES(password_hash),
    updated_at = NOW();

-- Add indexes for better performance
ALTER TABLE submissions ADD INDEX idx_created_at (created_at);
ALTER TABLE submissions ADD INDEX idx_email (email);
ALTER TABLE submissions ADD INDEX idx_status (status) IF NOT EXISTS;

-- Add status column to submissions table if it doesn't exist
ALTER TABLE submissions 
ADD COLUMN status ENUM('pending', 'under_review', 'accepted', 'rejected', 'published') DEFAULT 'pending' AFTER article_synopsis,
ADD COLUMN reviewer_notes TEXT AFTER status,
ADD COLUMN admin_notes TEXT AFTER reviewer_notes,
ADD COLUMN reviewed_by INT NULL AFTER admin_notes,
ADD COLUMN reviewed_at DATETIME NULL AFTER reviewed_by,
ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER reviewed_at,
ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Add foreign key for reviewer
ALTER TABLE submissions 
ADD FOREIGN KEY fk_reviewed_by (reviewed_by) REFERENCES admin_users(id) ON DELETE SET NULL;

-- Create view for admin dashboard statistics
CREATE OR REPLACE VIEW admin_dashboard_stats AS
SELECT 
    COUNT(*) as total_submissions,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_submissions,
    COUNT(CASE WHEN status = 'under_review' THEN 1 END) as under_review,
    COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted_submissions,
    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_submissions,
    COUNT(CASE WHEN status = 'published' THEN 1 END) as published_submissions,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_submissions,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_submissions,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as month_submissions
FROM submissions;