<?php
/**
 * Bun Framework
 *
 * @author Cristian Hampus
 * @version 0.0.1
 * @copyright Cristian Hampus, 04 April, 2011
 * @package Bun
 */

/**
 *  Check if the .htaccess file exists. 
 */
if (!file_exists('.htaccess')) {
    //throw new Exception('Bun needs an .htaccess file to work properly.');
}

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
    
    /**
     * Static variable containing the bun.
     *
     * @var Bun
     */
    private static $instance = NULL;

    private function __construct() {
        // Get the reqested path
        $this->path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '/';
    }

    /**
     * Get an instance of the bun. 
     * 
     * @return Bun
     */
    private static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new Bun();
        }

        return self::$instance;
    }

    /**
     * Checks if the route matches. 
     * 
     * @param string $method 
     * @param string $path 
     * @param callback $callback 
     * @return void
     */
    public static function route($method, $path, $callback)
    {
        // Get bun
        $bun = Bun::instance();
        
        // Check if a route already has been matched
        if ($bun->route_matched) {
            return FALSE;
        }      
        
        if ($method === $_SERVER['REQUEST_METHOD']) {
            if ($bun->matchRoute($path)) {
                // Call function with arguments.
                call_user_func($callback, $bun->getArguments($path));
                
                // Route has been matched, stop matchin!
                return $bun->route_matched = TRUE;
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
    public static function render($engine, $template, $data = array())
    {    
        $bun = Bun::instance();
        $bun->view_data = $data;

        switch ($engine) {
            case 'mustache':
                return $bun->renderWithMustache($template);
                break;
            
            default:
                return $bun->renderWithPhp($template);
                break;
        }
    }

    private function renderWithPhp($template)
    {
        $bun = Bun::instance();
        
        // Start output buffering
        ob_start();
        
        // Extract view data array to local variables
        extract($bun->view_data);
        
        // Unset the bun
        unset($bun);

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
        // Get bun
        $bun = Bun::instance();

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
        return preg_match(sprintf('/^%s$/', $path), $bun->path);
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
        $bun = Bun::instance();
        return array_diff(explode('/', $bun->path), explode('/', $path));
    }

    /**
     * Get the current url.
     * 
     * @return stdClass
     */
    public function getCurrentUrl()
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
        $url .= sprintf('://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME']);

        $url .= (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '';
        return $this->arrayToObject(parse_url($url));
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

/**
 *  Below comes the public API for the framework. 
 */

/**
 * Define a GET handler for path.  
 * 
 * @param string $path 
 * @param callback $callback 
 * @access public
 * @return void
 */
function get($path, $callback) {
    Bun::route('GET', $path, $callback);
}

/**
 * Define a POST handler for path.
 * 
 * @param string $path 
 * @param callback $callback 
 * @access public
 * @return void
 */
function post($path, $callback) {
    Bun::route('POST', $path, $callback);
}

/**
 * Define a PUT handler for path.
 * 
 * @param string $path 
 * @param callback $callback 
 * @access public
 * @return void
 */
function put($path, $callback) {
    Bun::route('PUT', $path, $callback);
}

/**
 * Define a DELETE handler for path.
 * 
 * @param string $path 
 * @param callback $callback 
 * @access public
 * @return void
 */
function delete($path, $callback) {
    Bun::route('DELETE', $path, $callback);
}

/**
 * Render a Mustache template. 
 * 
 * @param string $template 
 * @param array $data 
 * @access public
 * @return void
 */
function mustache($template, $data = array())
{
    echo Bun::render('mustache', $template, $data);
}

/**
 * Render a PHP template. 
 * 
 * @param string $template 
 * @param array $data 
 * @access public
 * @return void
 */
function render($template, $data = array()) {
    echo Bun::render('php', $template, $data); 
}
