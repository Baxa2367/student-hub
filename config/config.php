<?php
/**
 * Configuration File
 * 
 * Central configuration for the Student Hub application.
 * Database credentials, app settings, and constants.
 */

return [
    // Application Settings
    'app_name' => 'Student Hub',
    'app_version' => '1.0.0',
    'app_url' => 'http://localhost/student-hub',
    'app_timezone' => 'UTC',
    'debug_mode' => true,

    // Database Configuration
    'db_host' => 'localhost',
    'db_name' => 'student_hub',
    'db_user' => 'root',
    'db_pass' => '',
    'db_port' => 3306,

    // Session Configuration
    'session_lifetime' => 3600,
    'session_name' => 'student_hub_session',

    // File Upload Settings
    'max_upload_size' => 5242880, // 5MB
    'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png', 'gif'],
    'upload_path' => 'public/uploads/',

    // Language Settings
    'default_language' => 'en',
    'supported_languages' => ['en', 'ru', 'kz'],

    // Pagination
    'items_per_page' => 10,

    // Roles
    'roles' => [
        'admin' => 1,
        'teacher' => 2,
        'student' => 3,
    ],
];
