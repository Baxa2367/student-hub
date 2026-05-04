<?php
/**
 * App.php - Router and Application Bootstrap
 * 
 * Handles routing, request parsing, and controller instantiation.
 * Implements basic MVC router pattern.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Core;

class App
{
    /**
     * @var string Current controller
     */
    protected $controller = 'Home';

    /**
     * @var string Current method/action
     */
    protected $method = 'index';

    /**
     * @var array URL parameters
     */
    protected $params = [];

    /**
     * Constructor - Parse URL and prepare routing
     */
    public function __construct()
    {
        $this->parseUrl();
    }

    /**
     * Parse the URL from REQUEST_URI
     * URL format: /student-hub/controller/method/param1/param2
     */
    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            // Split URL by / and remove empty values
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            // First segment is controller
            if (!empty($url[0])) {
                $this->controller = ucwords($url[0]);
            }

            // Second segment is method
            if (!empty($url[1])) {
                $this->method = $url[1];
            }

            // Remaining segments are parameters
            $this->params = array_values($url);
            if (!empty($this->params)) {
                array_shift($this->params); // Remove controller
                if (!empty($this->params)) {
                    array_shift($this->params); // Remove method
                }
            }
        }
    }

    /**
     * Run the application by loading controller and executing method
     */
    public function run()
    {
        // Build controller class name
        $controllerName = "App\\Controllers\\" . $this->controller . "Controller";

        // Check if controller file exists
        $controllerFile = __DIR__ . "/../controllers/" . $this->controller . "Controller.php";

        if (!file_exists($controllerFile)) {
            // If controller doesn't exist, use 404
            $this->show404();
            return;
        }

        // Create controller instance
        $controller = new $controllerName();

        // Check if method exists
        if (!method_exists($controller, $this->method)) {
            $this->show404();
            return;
        }

        // Call the method with parameters
        call_user_func_array([$controller, $this->method], $this->params);
    }

    /**
     * Display 404 error page
     */
    private function show404()
    {
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The page you are looking for does not exist.</p>";
    }
}
