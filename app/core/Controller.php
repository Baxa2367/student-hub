<?php
/**
 * Controller.php - Base Controller Class
 * 
 * All controllers extend this base class.
 * Provides common functionality like view rendering and data passing.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Core;

class Controller
{
    /**
     * Load a view file and pass data
     * 
     * @param string $view View file name (without .php)
     * @param array $data Data to pass to view
     */
    protected function view($view, $data = [])
    {
        // Check if view file exists
        $viewFile = __DIR__ . "/../views/" . $view . ".php";

        if (!file_exists($viewFile)) {
            die("View file not found: " . $viewFile);
        }

        // Extract data variables for use in view
        extract($data);

        // Include the view file
        require_once $viewFile;
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current authenticated user ID
     * 
     * @return int|null
     */
    protected function getCurrentUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user's role
     * 
     * @return string|null
     */
    protected function getCurrentUserRole()
    {
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Redirect to another page
     * 
     * @param string $url URL to redirect to
     */
    protected function redirect($url)
    {
        header("Location: " . $url);
        exit();
    }

    /**
     * Check if user has required role
     * 
     * @param string $role Required role
     * @return bool
     */
    protected function hasRole($role)
    {
        return $this->getCurrentUserRole() === $role;
    }

    /**
     * Check if user is teacher or admin
     * 
     * @return bool
     */
    protected function isTeacherOrAdmin()
    {
        $role = $this->getCurrentUserRole();
        return $role === 'teacher' || $role === 'admin';
    }

    /**
     * Get JSON request data
     * 
     * @return array
     */
    protected function getJsonData()
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }

    /**
     * Return JSON response
     * 
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit();
    }

    /**
     * Set flash message for next request
     * 
     * @param string $message Message text
     * @param string $type Type: success, error, warning, info
     */
    protected function setFlash($message, $type = 'info')
    {
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * Get and clear flash message
     * 
     * @return array|null
     */
    protected function getFlash()
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
