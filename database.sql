-- ========================================
-- STUDENT HUB - DATABASE SCHEMA
-- ========================================
-- Database: student_hub
-- Author: Senior Full Stack Developer
-- Date: 2026-05-04
-- ========================================

-- Create Database
CREATE DATABASE IF NOT EXISTS `student_hub`;
USE `student_hub`;

-- ========================================
-- 1. ROLES TABLE
-- ========================================
CREATE TABLE `roles` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles
INSERT INTO `roles` (`name`, `description`) VALUES
('admin', 'Administrator with full system access'),
('teacher', 'Teacher who creates courses and posts assignments'),
('student', 'Student who joins courses and completes assignments');

-- ========================================
-- 2. USERS TABLE
-- ========================================
CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL DEFAULT 3,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX idx_email (`email`),
  INDEX idx_role_id (`role_id`),
  INDEX idx_created_at (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 3. COURSES TABLE
-- ========================================
CREATE TABLE `courses` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `teacher_id` INT NOT NULL,
  `category` VARCHAR(100),
  `thumbnail` VARCHAR(255),
  `is_published` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`teacher_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_teacher_id (`teacher_id`),
  INDEX idx_is_published (`is_published`),
  INDEX idx_created_at (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 4. COURSE_USERS TABLE (Many-to-Many)
-- ========================================
CREATE TABLE `course_users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `role` ENUM('student', 'teacher', 'assistant') DEFAULT 'student',
  `progress` INT DEFAULT 0,
  `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_user_course (user_id, course_id),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_user_id (`user_id`),
  INDEX idx_course_id (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 5. POSTS TABLE
-- ========================================
CREATE TABLE `posts` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `type` ENUM('assignment', 'announcement', 'lesson', 'discussion') DEFAULT 'discussion',
  `due_date` DATETIME,
  `is_pinned` BOOLEAN DEFAULT FALSE,
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_user_id (`user_id`),
  INDEX idx_course_id (`course_id`),
  INDEX idx_type (`type`),
  INDEX idx_is_pinned (`is_pinned`),
  FULLTEXT ft_title_content (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 6. COMMENTS TABLE
-- ========================================
CREATE TABLE `comments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `parent_comment_id` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`parent_comment_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_post_id (`post_id`),
  INDEX idx_user_id (`user_id`),
  INDEX idx_created_at (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 7. MATERIALS TABLE (File uploads)
-- ========================================
CREATE TABLE `materials` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `post_id` INT,
  `course_id` INT,
  `user_id` INT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(50),
  `file_size` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_course_id (`course_id`),
  INDEX idx_post_id (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- 8. NOTIFICATIONS TABLE
-- ========================================
CREATE TABLE `notifications` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `type` VARCHAR(50),
  `title` VARCHAR(255),
  `message` TEXT,
  `related_id` INT,
  `is_read` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX idx_user_id (`user_id`),
  INDEX idx_is_read (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- DML - DATA MANIPULATION LANGUAGE EXAMPLES
-- ========================================

-- INSERT: Create sample users
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `bio`) VALUES
('John Teacher', 'john@example.com', '$2y$10$YourHashedPassword1', 2, 'Passionate educator'),
('Alice Student', 'alice@example.com', '$2y$10$YourHashedPassword2', 3, 'Eager learner'),
('Bob Student', 'bob@example.com', '$2y$10$YourHashedPassword3', 3, 'Computer Science enthusiast'),
('Emma Admin', 'admin@example.com', '$2y$10$YourHashedPassword4', 1, 'System administrator');

-- INSERT: Create sample courses
INSERT INTO `courses` (`name`, `description`, `teacher_id`, `category`, `is_published`) VALUES
('Introduction to PHP', 'Learn PHP from basics to advanced', 1, 'Programming', TRUE),
('Web Development Fundamentals', 'HTML, CSS, JavaScript basics', 1, 'Web Development', TRUE),
('Database Design', 'Learn MySQL and database design', 1, 'Database', FALSE);

-- INSERT: Enroll students in courses
INSERT INTO `course_users` (`user_id`, `course_id`, `role`) VALUES
(2, 1, 'student'),
(2, 2, 'student'),
(3, 1, 'student'),
(3, 2, 'student'),
(3, 3, 'student');

-- INSERT: Create sample posts
INSERT INTO `posts` (`user_id`, `course_id`, `title`, `content`, `type`, `due_date`) VALUES
(1, 1, 'First Assignment: Variables and Data Types', 'Create a PHP script that demonstrates understanding of variables and data types. Submit by Friday.', 'assignment', DATE_ADD(NOW(), INTERVAL 3 DAY)),
(1, 1, 'Course Introduction Announcement', 'Welcome to Introduction to PHP! This course covers all basics...', 'announcement', NULL),
(1, 2, 'CSS Layout Techniques Lesson', 'Today we will learn about Flexbox and Grid...', 'lesson', NULL),
(2, 1, 'Question about array functions', 'Can someone explain the difference between array_map and array_filter?', 'discussion', NULL);

-- INSERT: Create sample comments
INSERT INTO `comments` (`post_id`, `user_id`, `content`) VALUES
(4, 1, 'Great question! array_map applies a function to every element...'),
(4, 3, 'This really helped me understand the difference!'),
(1, 2, 'Thanks for the assignment! Started working on it.'),
(1, 3, 'Is this assignment due on Friday or Sunday?');
