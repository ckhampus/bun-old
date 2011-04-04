<?php
/**
 * Bun Framework
 *
 * @author Cristian Hampus
 * @version 0.0.1
 * @copyright Me, 04 April, 2011
 * @package Bun
 */

/**
 *  Check if the .htaccess file exists. 
 */
if (!file_exists('.htaccess')) {
    //throw new Exception('Bun needs an .htaccess file to work properly.');
}

class Bun {
    /**
     * True of route has been matched.  
     * 
     * @var bool
     */
    private $route_matched = FALSE;
    
    /**
     * Static variable containing the bun.
     *
     * @var Bun
     */
    private static $instance = NULL;

    private function __construct() {
        
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
        $bun = Bun::instance();

        if ($bun->route_matched) {
            return FALSE;
        }

        $requested_path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '/';

        if ($method === $_SERVER['REQUEST_METHOD']) {
            if ($path === $requested_path) {
                call_user_func($callback);

                return $bun->route_matched = TRUE;
            }
        }
    }

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
     * @param Array $array 
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
 *  The public API for the framework. 
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
