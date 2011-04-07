<?php
/**
 * @package Core 
 */

// Include bun
require('Bun/Bun.php');

// Create a new bun
$GLOBALS['bun'] = new Bun();

// Check if htaccess file exists
if (!file_exists('.htaccess')) {
    //throw new Exception('Bun needs an .htaccess file to work properly.');
}

/**
 *  Below comes the public API for the framework. 
 */

/**
 * Define a GET handler for path.  
 * 
 * @param string $path 
 * @param callback $callback
 * @param int $lifetime
 * @access public
 * @return void
 */
function get($path, $callback, $lifetime = 0) {
    return $GLOBALS['bun']->route('GET', $path, $callback, $lifetime);
}

/**
 * Define a POST handler for path.
 * 
 * @param string $path 
 * @param callback $callback 
 * @param int $lifetime
 * @access public
 * @return void
 */
function post($path, $callback, $lifetime = 0) {
    return $GLOBALS['bun']->route('POST', $path, $callback, $lifetime);
}

/**
 * Define a PUT handler for path.
 * 
 * @param string $path 
 * @param callback $callback 
 * @param int $lifetime
 * @access public
 * @return void
 */
function put($path, $callback, $lifetime = 0) {
    return $GLOBALS['bun']->route('PUT', $path, $callback, $lifetime);
}

/**
 * Define a DELETE handler for path.
 * 
 * @param string $path 
 * @param callback $callback 
 * @param int $lifetime
 * @access public
 * @return void
 */
function delete($path, $callback, $lifetime = 0) {
    return $GLOBALS['bun']->route('DELETE', $path, $callback, $lifetime);
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
    echo $GLOBALS['bun']->render('mustache', $template, $data);
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
    echo $GLOBALS['bun']->render('php', $template, $data); 
}

/**
 * Set a name for the following route. 
 * 
 * @param string $name 
 * @access public
 * @return void
 */
function name($name) {
    $GLOBALS['bun']->setRouteName($name);
}

/**
 * Return a route object by name. 
 * 
 * @param mixed $name 
 * @access public
 * @return Route
 */
function route($name) {
    return $GLOBALS['bun']->getRouteByName($name);
}

/**
 * Returns the path for a named route. 
 * 
 * @param string $name 
 * @param mixed $values 
 * @access public
 * @return string
 */
function path($name, $values = array()) {
    if (!is_array($values) && func_num_args() > 0) {
        $values = func_get_args();
        array_shift($values);
    }
    
    $route = $GLOBALS['bun']->getRouteByName($name);
    if ($route->hasParameters() && count($values) === 0) {
        throw new Exception('Path requires parameters.');
    }
    
    return $route->getRealPath($values);
}

$GLOBALS['bun']->countRoutes();


// DOC: (@name|@before) +([a-zA-Z]+)
