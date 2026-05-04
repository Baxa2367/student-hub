<?php
/**
 * App.php
 * 
 * Main application router and bootstrapper.
 * Handles URL routing and dispatches requests to controllers.
 */

namespace App\Core;

class App
{
    /**
     * @var string Base URL
     */
    protected $baseUrl = '';

    /**
     * @var array Routes configuration
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /**
     * @var string Current request method
     */
    protected $method;

    /**
     * @var string Current URI
     */
    protected $uri;

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = require(__DIR__ . '/../../config/config.php');
        $this->baseUrl = $config['app_url'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $this->parseUri();
    }

    /**
     * Parse URI from request
     * 
     * @return string
     */
    protected function parseUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $baseUrlPath = parse_url($this->baseUrl, PHP_URL_PATH);
        
        if (!empty($baseUrlPath)) {
            $uri = str_replace($baseUrlPath, '', $uri);
        }

        $uri = trim($uri, '/');
        $uri = rtrim($uri, '/');

        // Remove query string
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        return $uri ?: 'index';
    }

    /**
     * Register GET route
     * 
     * @param string $path
     * @param string $handler Controller@method
     * @return void
     */
    public function get($path, $handler)
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Register POST route
     * 
     * @param string $path
     * @param string $handler Controller@method
     * @return void
     */
    public function post($path, $handler)
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch request to appropriate controller
     * 
     * @return void
     */
    public function dispatch()
    {
        $handler = $this->getMatchingRoute();

        if (!$handler) {
            $this->notFound();
            return;
        }

        $this->executeHandler($handler);
    }

    /**
     * Get matching route for current request
     * 
     * @return string|null
     */
    protected function getMatchingRoute()
    {
        $routes = $this->routes[$this->method] ?? [];

        // Exact match
        if (isset($routes[$this->uri])) {
            return $routes[$this->uri];
        }

        // Pattern match with parameters
        foreach ($routes as $path => $handler) {
            if ($this->matchRoute($path, $this->uri)) {
                return $handler;
            }
        }

        return null;
    }

    /**
     * Check if route matches URI pattern
     * 
     * @param string $path
     * @param string $uri
     * @return bool
     */
    protected function matchRoute($path, $uri)
    {
        $pathParts = explode('/', $path);
        $uriParts = explode('/', $uri);

        if (count($pathParts) !== count($uriParts)) {
            return false;
        }

        foreach ($pathParts as $key => $part) {
            if (strpos($part, '{') === 0) {
                continue; // Parameter, skip
            }
            if ($part !== $uriParts[$key]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute controller handler
     * 
     * @param string $handler Controller@method
     * @return void
     */
    protected function executeHandler($handler)
    {
        [$controller, $method] = explode('@', $handler);

        $controllerClass = 'App\\Controllers\\' . $controller;

        if (!class_exists($controllerClass)) {
            die("Controller not found: {$controllerClass}");
        }

        $instance = new $controllerClass();

        if (!method_exists($instance, $method)) {
            die("Method not found: {$method}");
        }

        call_user_func([$instance, $method]);
    }

    /**
     * Handle 404 Not Found
     * 
     * @return void
     */
    protected function notFound()
    {
        http_response_code(404);
        echo "404 - Page Not Found";
        exit;
    }
}
