<?php
/**
 * Bun Framework
 *
 * @author Cristian Hampus
 * @version 0.0.1
 * @copyright Me, 04 April, 2011
 * @package Bun
 */

if (file_exists('.htaccess')) {
    throw new Exception('Bun needs an .htaccess file to work properly.');
}

class Bun {
    public function route($method, $path, $callback)
    {
        
    }

    public function router()
    {
        
    }
}

$bun = new Bun();


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
    $bun->route('GET', $path, $callback);
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
    $bun->route('POST', $path, $callback);
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
    $bun->route('PUT', $path, $callback);
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
    $bun->route('DELETE', $path, $callback);
}
