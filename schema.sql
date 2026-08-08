CREATE DATABASE IF NOT EXISTS scholarship_db;
USE scholarship_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'student',
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS scholarships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS student_scholarships (
    student_id INT NOT NULL,
    scholarship_id INT NOT NULL,
    parents_income FLOAT,
    parents_occupation VARCHAR(150),
    purpose VARCHAR(255),
    gpa FLOAT,
    permanent_address TEXT,
    nic VARCHAR(20),
    contact_numbers VARCHAR(50),
    description TEXT,
    testimonial_checked INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, scholarship_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, role, email) VALUES
('admin', 'admin123', 'admin', 'admin@example.com'),
('ucsc', 'ucsc', 'student', 'student@example.com'),
('registrar', 'registrar123', 'registrar', 'registrar@example.com');

INSERT INTO scholarships (name, description, deadline) VALUES
('Academic Excellence Award', 'For students with a GPA of 3.5 or above. Covers tuition fees for one academic year.', '2026-12-31'),
('Financial Assistance Grant', 'For students from low-income families. Requires proof of household income and a Grama Niladhari testimonial.', '2026-12-31'),
('Sports Achievement Scholarship', 'For students who have represented the university or national teams in sports.', '2026-12-31');
