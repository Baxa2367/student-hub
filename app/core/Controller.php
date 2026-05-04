<?php
/**
 * Controller.php
 * 
 * Base Controller class - all controllers inherit from this.
 * Handles view rendering and common functionality.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Core;

class Controller
{
    /**
     * @var array Data to pass to view
     */
    protected $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize common properties
        $this->data['app_name'] = 'Student Hub';
        $this->data['user'] = $_SESSION['user'] ?? null;
    }

    /**
     * Render a view file
     * 
     * @param string $view View file path (without .php)
     * @param array $data Data to pass to view
     * @return void
     */
    protected function render($view, $data = [])
    {
        // Merge data with default data
        $this->data = array_merge($this->data, $data);
        
        // Extract data to variables
        extract($this->data);
        
        // Build view path
        $viewPath = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            die("View not found: {$viewPath}");
        }
        
        // Load view
        require_once($viewPath);
    }

    /**
     * Redirect to a URL
     * 
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Set HTTP status code
     * 
     * @param int $code
     * @return void
     */
    protected function setStatus($code)
    {
        http_response_code($code);
    }

    /**
     * Return JSON response
     * 
     * @param array $data
     * @param int $status
     * @return void
     */
    protected function json($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit();
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user']);
    }

    /**
     * Get current user
     * 
     * @return array|null
     */
    protected function getCurrentUser()
    {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Check if user has specific role
     * 
     * @param string $role
     * @return bool
     */
    protected function hasRole($role)
    {
        return $this->isAuthenticated() && $_SESSION['user']['role'] === $role;
    }

    /**
     * Check if user is admin
     * 
     * @return bool
     */
    protected function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is teacher
     * 
     * @return bool
     */
    protected function isTeacher()
    {
        return $this->hasRole('teacher');
    }

    /**
     * Check if user is student
     * 
     * @return bool
     */
    protected function isStudent()
    {
        return $this->hasRole('student');
    }

    /**
     * Require authentication
     * Redirects to login if not authenticated
     * 
     * @return void
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            header('Location: /student-hub/public/index.php?route=auth/login');
            exit();
        }
    }

    /**
     * Require specific role
     * Returns 403 if user doesn't have role
     * 
     * @param string $role
     * @return void
     */
    protected function requireRole($role)
    {
        $this->requireAuth();
        
        if (!$this->hasRole($role)) {
            $this->setStatus(403);
            die('Access Denied');
        }
    }
}
