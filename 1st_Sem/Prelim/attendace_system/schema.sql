
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','admin') NOT NULL
);

-- Courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,
    course_time TIME NULL
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_num VARCHAR(50) NOT NULL,
    user_id INT,
    year_level INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Student-Courses junction table
CREATE TABLE student_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_course (student_id, course_id) 
);

-- Attendance table 
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_course_id INT NOT NULL,
    date DATE,
    time_in TIME,
    status ENUM('Present','Absent') DEFAULT 'Present',
    excuse_status ENUM('Pending','Accepted','Rejected') DEFAULT NULL, 
    is_late BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (student_course_id) REFERENCES student_courses(id) ON DELETE CASCADE
);


-- Add excuse_letters table
CREATE TABLE excuse_letters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attendance_id INT NOT NULL,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    letter_text TEXT NOT NULL,
    status ENUM('Pending','Accepted','Rejected') DEFAULT 'Pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    FOREIGN KEY (attendance_id) REFERENCES attendance(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

