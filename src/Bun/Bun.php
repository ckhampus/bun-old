<?php

require('Base.php');
require('Cache.php');
require('Route.php');

/**
 * Bun
 *
 * The Bun class is the main class of the
 * framework. It does most of the heavy lifting.
 *
 * @package Core
 */
class Bun extends Base{
    /**
     * True if a route has been matched.  
     * 
     * @var bool
     */
    protected $route_matched = FALSE;

    /**
     * Contains the number of defined routes. 
     * 
     * @var int
     */
    protected $number_of_routes = 0;

    /**
     * The number of how many routes have been executed. 
     * 
     * @var int
     */
    protected $routes_executed = 0;

    /**
     * The currently requested path. 
     * 
     * @var string
     */
    protected $requested_path = '/';

    /**
     * Array containing all routes. 
     * 
     * @var array
     */
    protected $routes = array();

    /**
     * Array containing data to pass to the template file. 
     * 
     * @var array
     */
    protected $view_data = array();
    
    protected $name_for_new_route = NULL;
    
    /**
     * Creates an instance of the Bun.
     * 
     * @access public
     */
    function __construct() {
        // Get the requested path
        $this->requested_path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '/';
    }

    /**
     * Checks if the route matches. 
     * 
     * @param string $method 
     * @param string $path 
     * @param callback $callback 
     * @return void
     */
    public function route($method, $path, $callback, $lifetime = 0)
    {
        $route = new Route($method, $path, $callback, $lifetime);
        
        if (!is_null($this->name_for_new_route)) {
            $route->name = $this->name_for_new_route;
            $this->name_for_new_route = NULL;
        }
        
        $this->routes[] = $route;
        
        $this->routes_executed++;

        if ($this->number_of_routes == $this->routes_executed) {
            if (!$this->router()) {
                $this->notFound();
                return FALSE;
            }
        }

        return $route;
    }
    
    public function setRouteName($name) {
        $this->name_for_new_route = $name;
    }
    
    public function getRouteByName($name) {
        foreach ($this->routes as $route) {
            if ($route->name === $name) {
                return $route;
            }
        }
        
        return FALSE;
    }

    /**
     * Handles the routing.
     * 
     * @return void
     */
    public function router()
    {
        foreach ($this->routes as $route) {
            $cache = new Cache($route->path, $route->lifetime);   

            if ($route->method === $_SERVER['REQUEST_METHOD']) {
                if ($route->matchRoute($this->requested_path)) {
                 
                    // Check if page is cached or not
                    if (!$cache->start()) {
                        // Call function with arguments.
                        call_user_func_array($route->callback, $this->getArguments($route->path));
                        $cache->end();
                    }
            
                    // Route has been matched, stop matchin!
                    return $this->route_matched = TRUE;
                }
            }
        }
        
        return FALSE;
    }

    /**
     * Error handler for page not found error. 
     * 
     * @return void
     */
    public function notFound()
    {
        echo 'Page not found';
    }

    /**
     * Set error handler for specific status codes. 
     * 
     * @param int $status 
     * @param callback $callback
     */
    public function errorHandler($status, $callback)
    {
        // code...
    }

    /**
     * Redirect to location. 
     * 
     * @param string $location 
     * @param int $status 
     */
    public function redirect($location, $status)
    {
        
    }

    /**
     * Render template file using the specified engine. 
     * 
     * @param string $engine 
     * @param string $template 
     * @param array $data 
     * @return string
     */
    public function render($engine, $template, $data = array())
    {    
        $this->view_data = $data;

        switch ($engine) {
            case 'mustache':
                return $this->renderWithMustache($template);
            case 'php':
            default:
                return $this->renderWithPhp($template);
        }
    }

    /**
     * Count and return the number of defined routes. 
     * 
     * @return int
     */
    public function countRoutes()
    {
        $app_file = file_get_contents($_SERVER['SCRIPT_FILENAME']);

        $tokens = token_get_all($app_file);
        
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_STRING) {
                    switch ($token[1]) {
                        case 'get':
                        case 'post':
                        case 'put':
                        case 'delete':
                            $this->number_of_routes++;
                            break;
                    }
                }
            }
        }

        return $this->number_of_routes;
    }

    /**
     * Render Mustache template. 
     * 
     * @param string $template 
     * @return string
     */
    private function renderWithMustache($template)
    {
        $path = realpath(__DIR__.'/../../vendor/mustache/Mustache.php');

        if (!class_exists('Mustache')) {
            
            if (!file_exists($path)) {
                throw new Exception('Mustache framework could not be loaded');
            }
            
            include($path);
        }

        if (class_exists('Mustache')) {
            $mustache = new Mustache();

            $partials = array();

            foreach ($this->view_data as $key => $value) {
                $partial = realpath($value);

                if (file_exists($partial)) {
                    $partials[$key] = file_get_contents($partial);
                    unset($this->view_data[$key]);
                }
            }

            return $mustache->render(file_get_contents(realpath($template)), $this->view_data, $partials);
        }
    }

    /**
     * Render PHP template. 
     * 
     * @param string $template 
     * @return string
     */
    private function renderWithPhp($template)
    {
        // Extract variables in to the
        // local scope for use in template.
        extract($this->view_data);
        
        // Start output buffering
        ob_start();
        
        set_error_handler(function($errno, $errstr) {
            
        }, E_NOTICE);

        // Include the template
        include($template);

        // Flush and return the buffer.
        return ob_get_clean();
    }

    /**
     * Get the arguments from the path.
     * 
     * @param string $path 
     * @return array
     */
    private function getArguments($path)
    {
        // Get bun
        //$bun = Bun::instance();
        return array_diff(explode('/', $this->requested_path), explode('/', $path));
    }

    /**
     * Turns an array to an object and returns it. 
     * 
     * @param array $array 
     * @return stdClass
     */
    private function arrayToObject(Array $array)
    {
        $object = new stdClass;

        foreach ($array as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }
}
