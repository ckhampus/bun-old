<?php
/**
 * @package Core 
 */

require_once('Bun/Bun.php');

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
    $GLOBALS['bun']->route('GET', $path, $callback, $lifetime);
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
    $GLOBALS['bun']->route('POST', $path, $callback, $lifetime);
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
    $GLOBALS['bun']->route('PUT', $path, $callback, $lifetime);
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
    $GLOBALS['bun']->route('DELETE', $path, $callback, $lifetime);
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

$GLOBALS['bun']->countRoutes();
