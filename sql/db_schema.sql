-- Drop database if exists and create new one
DROP DATABASE IF EXISTS school_manage;
CREATE DATABASE school_manage;
USE school_manage;

-- 1. Person table (base table for all users)
CREATE TABLE person (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    role ENUM('student', 'teacher', 'parent', 'admin') NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- 2. Rooms table (no classes table needed)
CREATE TABLE rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    number VARCHAR(20) NOT NULL,
    type ENUM('classroom', 'lab', 'auditorium') NOT NULL, 
    disponibility ENUM('available', 'occupied') NOT NULL
);

-- 3. Subjects table (fixed syntax)
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- 4. Teachers table
CREATE TABLE teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    person_id INT NOT NULL,
    employee_number VARCHAR(20) UNIQUE NOT NULL,
    specialty VARCHAR(100),
    FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE
);

-- 5. Students table (removed class_id, using room_id instead)
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    person_id INT NOT NULL,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    room_id INT,
    FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

-- 6. Parents table
CREATE TABLE parents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    person_id INT NOT NULL,
    student_id INT NOT NULL,
    FOREIGN KEY (person_id) REFERENCES person(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 7. Courses table
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    room_id INT NOT NULL,
    duration VARCHAR(20) NOT NULL,
    level VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- 8. Schedules table
CREATE TABLE schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    room_id INT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- 9. Evaluations table (fixed missing comma)
CREATE TABLE evaluations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    type ENUM('test', 'exam', 'homework', 'project') NOT NULL,
    date_evaluation DATE NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);

-- 10. Grades table
CREATE TABLE grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    evaluation_id INT NOT NULL,
    student_id INT NOT NULL,
    score DECIMAL(5,2),
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- 11. Bulletins table (fixed syntax)
CREATE TABLE bulletins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    evaluation_id INT NOT NULL,
    grade VARCHAR(20),
    general_average DECIMAL(5,2),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
);

-- 12. School fees table
CREATE TABLE school_fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    type ENUM('tuition', 'registration', 'transport', 'other') NOT NULL
);

-- 13. Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    fee_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (fee_id) REFERENCES school_fees(id) ON DELETE CASCADE
);

-- 14. Salaries table
CREATE TABLE salaries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    month VARCHAR(20) NOT NULL,
    year INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATE,
    status ENUM('pending', 'paid') DEFAULT 'pending',
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);

-- 15. Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 16. Notification recipients table
CREATE TABLE notification_recipients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    notification_id INT NOT NULL,
    recipient_id INT NOT NULL,
    read_status BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES person(id) ON DELETE CASCADE
);

-- INSERT TEST DATA

-- 1. Insert persons (password is "123456" hashed)
INSERT INTO person (first_name, last_name, email, phone, role, password) VALUES 
('John', 'Admin', 'admin@school.com', '1234567890', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Sarah', 'Johnson', 'teacher1@school.com', '1234567891', 'teacher', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Mike', 'Wilson', 'teacher2@school.com', '1234567892', 'teacher', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Emma', 'Davis', 'teacher3@school.com', '1234567893', 'teacher', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Alex', 'Smith', 'student1@school.com', '1234567894', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Lisa', 'Brown', 'student2@school.com', '1234567895', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Tom', 'Miller', 'student3@school.com', '1234567896', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Kate', 'Garcia', 'student4@school.com', '1234567897', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('David', 'Martinez', 'student5@school.com', '1234567898', 'student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Maria', 'Lopez', 'parent1@school.com', '1234567899', 'parent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Robert', 'Taylor', 'parent2@school.com', '1234567800', 'parent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 2. Insert rooms
INSERT INTO rooms (number, type, disponibility) VALUES 
('101', 'classroom', 'available'),
('102', 'classroom', 'available'),
('103', 'classroom', 'occupied'),
('201', 'lab', 'available'),
('202', 'lab', 'available'),
('301', 'auditorium', 'available'),
('104', 'classroom', 'available'),
('105', 'classroom', 'available');

-- 3. Insert subjects
INSERT INTO subjects (name) VALUES 
('Mathematics'),
('Physics'),
('Chemistry'),
('Biology'),
('English Literature'),
('History'),
('Computer Science'),
('French'),
('Art'),
('Physical Education');

-- 4. Insert teachers
INSERT INTO teachers (person_id, employee_number, specialty) VALUES 
(2, 'T001', 'Mathematics'),
(3, 'T002', 'Physics'),
(4, 'T003', 'Computer Science');

-- 5. Insert students (using room_id instead of class_id)
INSERT INTO students (person_id, student_number, room_id) VALUES 
(5, 'S001', 1),
(6, 'S002', 1),
(7, 'S003', 2),
(8, 'S004', 7),
(9, 'S005', 8);

-- 6. Insert parents
INSERT INTO parents (person_id, student_id) VALUES 
(10, 1),
(11, 2);

-- 7. Insert courses
INSERT INTO courses (subject_id, teacher_id, room_id, duration, level, start_date, end_date) VALUES 
(1, 1, 1, '1 hour', 'Grade 10', '2024-01-15', '2024-06-15'),
(2, 2, 2, '1.5 hours', 'Grade 11', '2024-01-15', '2024-06-15'),
(7, 3, 4, '2 hours', 'Grade 12', '2024-01-15', '2024-06-15'),
(3, 2, 5, '1 hour', 'Grade 10', '2024-01-15', '2024-06-15'),
(5, 1, 7, '1 hour', 'Grade 11', '2024-01-15', '2024-06-15');

-- 8. Insert schedules
INSERT INTO schedules (course_id, room_id, date, start_time, end_time) VALUES 
(1, 1, '2024-01-15', '08:00:00', '09:00:00'),
(2, 2, '2024-01-15', '09:15:00', '10:45:00'),
(3, 4, '2024-01-15', '11:00:00', '13:00:00'),
(4, 5, '2024-01-16', '08:00:00', '09:00:00'),
(5, 7, '2024-01-16', '09:15:00', '10:15:00');

-- 9. Insert evaluations
INSERT INTO evaluations (subject_id, teacher_id, title, type, date_evaluation) VALUES 
(1, 1, 'Algebra Test', 'test', '2024-02-15'),
(2, 2, 'Physics Midterm', 'exam', '2024-03-01'),
(7, 3, 'Programming Project', 'project', '2024-02-28'),
(1, 1, 'Geometry Quiz', 'test', '2024-02-20'),
(3, 2, 'Chemistry Lab Report', 'homework', '2024-02-25');

-- 10. Insert grades
INSERT INTO grades (evaluation_id, student_id, score) VALUES 
(1, 1, 85.50),
(1, 2, 92.00),
(1, 3, 78.25),
(2, 4, 88.75),
(2, 5, 91.50),
(3, 4, 95.00),
(3, 5, 87.25),
(4, 1, 90.00),
(4, 2, 85.75),
(5, 1, 82.50);

-- 11. Insert bulletins
INSERT INTO bulletins (student_id, course_id, evaluation_id, grade, general_average) VALUES 
(1, 1, 1, 'B+', 87.75),
(2, 1, 1, 'A-', 88.87),
(3, 2, 2, 'B', 78.25),
(4, 3, 3, 'A', 91.87),
(5, 3, 3, 'A-', 89.37);

-- 12. Insert school fees
INSERT INTO school_fees (name, amount, type) VALUES 
('Tuition Fee - Semester 1', 2500.00, 'tuition'),
('Registration Fee', 150.00, 'registration'),
('Transport Fee - Monthly', 80.00, 'transport'),
('Lab Fee', 200.00, 'other'),
('Library Fee', 50.00, 'other'),
('Sports Fee', 100.00, 'other');

-- 13. Insert payments
INSERT INTO payments (student_id, fee_id, amount, payment_date, status) VALUES 
(1, 1, 2500.00, '2024-01-10', 'paid'),
(1, 2, 150.00, '2024-01-10', 'paid'),
(2, 1, 2500.00, '2024-01-12', 'paid'),
(2, 3, 80.00, '2024-01-15', 'paid'),
(3, 1, 1250.00, '2024-01-15', 'pending'),
(4, 2, 150.00, '2024-01-20', 'paid'),
(5, 1, 2500.00, '2024-01-25', 'pending');

-- 14. Insert salaries
INSERT INTO salaries (teacher_id, month, year, amount, payment_date, status) VALUES 
(1, 'January', 2024, 3500.00, '2024-01-31', 'paid'),
(2, 'January', 2024, 3800.00, '2024-01-31', 'paid'),
(3, 'January', 2024, 4000.00, '2024-01-31', 'paid'),
(1, 'February', 2024, 3500.00, NULL, 'pending'),
(2, 'February', 2024, 3800.00, NULL, 'pending'),
(3, 'February', 2024, 4000.00, NULL, 'pending');

-- 15. Insert notifications
INSERT INTO notifications (title, message, created_at) VALUES 
('Welcome to New Semester', 'Welcome back students! The new semester starts on January 15th, 2024.', '2024-01-10 09:00:00'),
('Parent-Teacher Meeting', 'Parent-teacher meetings are scheduled for February 20th, 2024.', '2024-01-15 10:30:00'),
('Exam Schedule Released', 'Mid-term examination schedule has been published. Check your student portal.', '2024-02-01 14:00:00'),
('Library Hours Extended', 'Library will remain open until 8 PM during exam period.', '2024-02-10 11:00:00'),
('Sports Day Announcement', 'Annual sports day will be held on March 15th, 2024.', '2024-02-05 16:00:00');

-- 16. Insert notification recipients
INSERT INTO notification_recipients (notification_id, recipient_id, read_status) VALUES 
(1, 5, TRUE),
(1, 6, FALSE),
(1, 7, TRUE),
(2, 10, FALSE),
(2, 11, TRUE),
(3, 5, FALSE),
(3, 6, FALSE),
(4, 8, TRUE),
(5, 9, FALSE);
