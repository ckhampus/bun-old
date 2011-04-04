<?php

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

    private $path;

    /**
     * Array containing data to pass to the template file. 
     * 
     * @var array
     */
    private $view_data = array();
    
    function __construct() {
        // Get the reqested path
        $this->path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '/';
    }

    /**
     * Checks if the route matches. 
     * 
     * @param string $method 
     * @param string $path 
     * @param callback $callback 
     * @return void
     */
    public function route($method, $path, $callback)
    {
        
        // Check if a route already has been matched
        if ($this->route_matched) {
            return FALSE;
        }      
        
        if ($method === $_SERVER['REQUEST_METHOD']) {
            if ($this->matchRoute($path)) {
                // Call function with arguments.
                call_user_func($callback, $this->getArguments($path));
                
                // Route has been matched, stop matchin!
                return $this->route_matched = TRUE;
            }
        }
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
                break;
            
            default:
                return $this->renderWithPhp($template);
                break;
        }
    }

    private function renderWithPhp($template)
    {

        // Start output buffering
        ob_start();
        
        // Extract view data array to local variables
        extract($this->view_data);

        // Include the template
        include($template);

        // Flush and return the buffer.
        return ob_get_flush();
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
        return preg_match(sprintf('/^%s$/', $path), $this->path);
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
        return array_diff(explode('/', $this->path), explode('/', $path));
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
