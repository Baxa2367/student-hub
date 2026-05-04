<?php
/**
 * Controller.php
 * 
 * Base Controller class for all application controllers.
 * Provides view rendering and common helper methods.
 */

namespace App\Core;

class Controller
{
    /**
     * @var array View data
     */
    protected $data = [];

    /**
     * @var string View path
     */
    protected $viewPath = 'app/views/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->checkAuth();
    }

    /**
     * Set view data
     * 
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Render view
     * 
     * @param string $view
     * @param array $data
     * @return void
     */
    public function render($view, $data = [])
    {
        $data = array_merge($this->data, $data);
        $viewFile = $this->viewPath . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: {$viewFile}");
        }

        extract($data);
        include $viewFile;
    }

    /**
     * Render JSON response
     * 
     * @param array $data
     * @param int $status
     * @return void
     */
    public function json($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to URL
     * 
     * @param string $url
     * @return void
     */
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function checkAuth()
    {
        // Override in child classes if needed
        return true;
    }

    /**
     * Check user role
     * 
     * @param string $role
     * @return bool
     */
    protected function hasRole($role)
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }
        return $_SESSION['user']['role'] === $role;
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
     * Set error message
     * 
     * @param string $message
     * @return void
     */
    protected function setError($message)
    {
        $_SESSION['error'] = $message;
    }

    /**
     * Set success message
     * 
     * @param string $message
     * @return void
     */
    protected function setSuccess($message)
    {
        $_SESSION['success'] = $message;
    }
}
