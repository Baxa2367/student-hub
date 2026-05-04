<?php
/**
 * Configuration file for Student Hub MVC System
 * 
 * Contains database credentials and application settings
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

// ========================================
// DATABASE CONFIGURATION
// ========================================
$config = [
    // Database credentials
    'db_host' => 'localhost',
    'db_name' => 'student_hub',
    'db_user' => 'root',
    'db_pass' => '', // Default XAMPP password is empty
    
    // Application settings
    'app_name' => 'Student Hub',
    'app_url' => 'http://localhost/student-hub',
    'app_timezone' => 'UTC',
    
    // Session settings
    'session_lifetime' => 3600, // 1 hour
    'session_path' => '/student-hub/',
    
    // File upload settings
    'upload_dir' => __DIR__ . '/../public/uploads/',
    'max_file_size' => 10485760, // 10MB in bytes
    'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'zip'],
    
    // Email settings (for notifications)
    'mail_from' => 'noreply@studenthub.local',
    'mail_host' => 'localhost',
    'mail_port' => 25,
    
    // Language settings
    'default_language' => 'en',
    'supported_languages' => ['en', 'ru', 'kz'],
    
    // Security settings
    'password_min_length' => 8,
    'password_require_special' => true,
    
    // Pagination
    'items_per_page' => 10,
    
    // Debug mode
    'debug' => true,
];

return $config;
