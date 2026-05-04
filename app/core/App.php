<?php
/**
 * App.php
 * 
 * Router - handles URL routing and controller dispatch.
 * Maps URLs to appropriate controllers and methods.
 * 
 * @author Senior Full Stack Developer
 * @version 1.0
 */

namespace App\Core;

class App
{
    /**
     * @var array Routes
     */
    protected $routes = [];

    /**
     * @var string Current route
     */
    protected $currentRoute;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registerRoutes();
    }

    /**
     * Register all application routes
     * 
     * @return void
     */
    protected function registerRoutes()
    {
        // Auth routes
        $this->routes = [
            'auth/register' => ['App\\Controllers\\AuthController', 'register'],
            'auth/login' => ['App\\Controllers\\AuthController', 'login'],
            'auth/logout' => ['App\\Controllers\\AuthController', 'logout'],
            'auth/post-register' => ['App\\Controllers\\AuthController', 'postRegister'],
            'auth/post-login' => ['App\\Controllers\\AuthController', 'postLogin'],

            // Home routes
            'home' => ['App\\Controllers\\HomeController', 'index'],
            'dashboard' => ['App\\Controllers\\DashboardController', 'index'],

            // Course routes
            'courses' => ['App\\Controllers\\CourseController', 'index'],
            'courses/create' => ['App\\Controllers\\CourseController', 'create'],
            'courses/store' => ['App\\Controllers\\CourseController', 'store'],
            'courses/view' => ['App\\Controllers\\CourseController', 'view'],
            'courses/enroll' => ['App\\Controllers\\CourseController', 'enroll'],
            'courses/edit' => ['App\\Controllers\\CourseController', 'edit'],
            'courses/update' => ['App\\Controllers\\CourseController', 'update'],
            'courses/delete' => ['App\\Controllers\\CourseController', 'delete'],

            // Post routes
            'posts/create' => ['App\\Controllers\\PostController', 'create'],
            'posts/store' => ['App\\Controllers\\PostController', 'store'],
            'posts/view' => ['App\\Controllers\\PostController', 'view'],
            'posts/edit' => ['App\\Controllers\\PostController', 'edit'],
            'posts/update' => ['App\\Controllers\\PostController', 'update'],
            'posts/delete' => ['App\\Controllers\\PostController', 'delete'],

            // Comment routes
            'comments/store' => ['App\\Controllers\\CommentController', 'store'],
            'comments/delete' => ['App\\Controllers\\CommentController', 'delete'],

            // User routes
            'users/profile' => ['App\\Controllers\\UserController', 'profile'],
            'users/edit' => ['App\\Controllers\\UserController', 'edit'],
            'users/update' => ['App\\Controllers\\UserController', 'update'],
        ];
    }

    /**
     * Run the application
     * 
     * @return void
     */
    public function run()
    {
        // Get the route from URL
        $route = isset($_GET['route']) ? trim($_GET['route'], '/') : 'home';

        $this->currentRoute = $route;

        if (!isset($this->routes[$route])) {
            http_response_code(404);
            die("Route not found: {$route}");
        }

        // Get controller and method
        list($controller, $method) = $this->routes[$route];

        // Check if controller class exists
        if (!class_exists($controller)) {
            die("Controller not found: {$controller}");
        }

        // Instantiate controller and call method
        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $method)) {
            die("Method not found: {$method} in {$controller}");
        }

        // Call the controller method
        call_user_func([$controllerInstance, $method]);
    }

    /**
     * Get current route
     * 
     * @return string
     */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }
}
