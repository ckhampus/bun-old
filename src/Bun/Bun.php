<?php

require_once('Cache.php');
require_once(realpath(__DIR__.'/../../vendor/mustache/Mustache.php'));

/**
 * Bun
 *
 * The Bun class is the main class of the
 * framework. It does most of the heavy lifting.
 */
class Bun {
    /**
     * True if a route has been matched.  
     * 
     * @var bool
     */
    private $route_matched = FALSE;

    /**
     * Contains the number of defined routes. 
     * 
     * @var int
     */
    private $number_of_routes = 0;

    /**
     * The currently requested path. 
     * 
     * @var string
     */
    private $requested_path = '/';

    /**
     * Array containing data to pass to the template file. 
     * 
     * @var array
     */
    private $view_data = array();
    
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
        // Check if a route already has been matched
        if (!$this->route_matched) {
            $cache = new Cache($path, $lifetime);   

            if ($method === $_SERVER['REQUEST_METHOD']) {
                if ($this->matchRoute($path)) {
                    
                    // Check if page is cached or not
                    if (!$cache->start()) {
                        // Call function with arguments.
                        call_user_func_array($callback, $this->getArguments($path));

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
                            $this->number_of_routes += 1;
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
     * Matches the route with the curent path. 
     * 
     * @param string $path 
     * @return bool
     */
    private function matchRoute($path)
    {
        // Contains regular expressions to replace
        $regexp = array(
            '/:[a-zA-Z_][a-zA-Z0-9_]*/' => '[\w]+',
            '/\*/' => '.+'
        );

        // Prepare the string
        $path = str_replace('/', '\/', $path);
        
        // Replace the reqular expressions
        foreach ($regexp as $key => $value) {
            $path = preg_replace($key, $value, $path);
        }
        
        // Matches the route with the current path
        return preg_match(sprintf('/^%s$/', $path), $this->requested_path);
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

$GLOBALS['bun'] = new Bun();
