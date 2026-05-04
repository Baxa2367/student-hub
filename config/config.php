<?php
/**
 * Configuration file for Student Hub application
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

return [
    // Database Configuration
    'db_host' => 'localhost',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'student_hub',

    // Application Settings
    'app_name' => 'Student Hub',
    'app_url' => 'http://localhost/student-hub',
    'app_debug' => true,
    'app_timezone' => 'UTC',

    // Session Configuration
    'session_timeout' => 3600, // 1 hour in seconds
    'session_name' => 'student_hub_session',

    // File Upload Configuration
    'upload_path' => __DIR__ . '/../public/uploads',
    'max_file_size' => 10485760, // 10MB in bytes
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip'],

    // Pagination
    'items_per_page' => 10,

    // Multilanguage Support
    'default_language' => 'en',
    'supported_languages' => ['en', 'ru', 'kz'],

    // Security
    'password_hash_algo' => PASSWORD_BCRYPT,
    'password_hash_options' => ['cost' => 10],
];
