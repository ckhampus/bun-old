<?php
/**
 * Bun Framework
 *
 * @author Cristian Hampus
 * @version 0.0.1
 * @copyright Cristian Hampus, 04 April, 2011
 * @package Bun
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
 * @access public
 * @return void
 */
function get($path, $callback) {
    $GLOBALS['bun']->route('GET', $path, $callback);
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
    $GLOBALS['bun']->route('POST', $path, $callback);
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
    $GLOBALS['bun']->route('PUT', $path, $callback);
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
    $GLOBALS['bun']->route('DELETE', $path, $callback);
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
