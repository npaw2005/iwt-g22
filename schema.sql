CREATE DATABASE IF NOT EXISTS scholarship_db;
USE scholarship_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student', 'registrar') NOT NULL DEFAULT 'student',
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS scholarships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    parents_income DECIMAL(12,2),
    parents_occupation VARCHAR(150),
    purpose VARCHAR(255),
    gpa DECIMAL(4,2),
    permanent_address TEXT,
    nic VARCHAR(20),
    contact_numbers VARCHAR(50),
    description TEXT,
    testimonial_checked TINYINT(1) DEFAULT 0,
    isApproved ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, role, email) VALUES
('admin', 'admin123', 'admin', 'admin@example.com'),
('ucsc', 'ucsc', 'student', 'student@example.com'),
('registrar', 'registrar123', 'registrar', 'registrar@example.com');
