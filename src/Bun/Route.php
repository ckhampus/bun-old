<?php

class Route {
    public $method;
    public $path;
    public $callback;
    public $lifetime;
    
    function __construct($method, $path, $callback, $lifetime = 0) {
        if (!is_int($lifetime)) {
            throw new Exception('Invalid lifetime.');
        }
        
        if (!is_callable($callback)) {
            throw new Exception('No valid callback passed.');
        }
        
        if (!in_array(strtoupper($method), array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new Exception('Method not supported.');
        }
        
        $this->method = $method;
        $this->path = $path;
        $this->callback = $callback;
        $this->lifetime = $lifetime;
    }
}