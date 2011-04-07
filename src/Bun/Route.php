<?php

require('Base.php');

class Route extends Base {    
    function __construct($method, $path, $callback, $lifetime = 0) {
        if (!in_array(strtoupper($method), array('GET', 'POST', 'PUT', 'DELETE'))) {
            throw new InvalidArgumentException('Method not supported.');
        }
        
        if (substr($path, 0, 1) !== '/') {
            throw new InvalidArgumentException('Path must start with a forward slash.');
        }
        
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('No valid callback passed.');
        }
    
        if (!is_int($lifetime)) {
            throw new InvalidArgumentException('Invalid lifetime.');
        }
        
        $this->addPublicProperty('method');
        $this->addPublicProperty('path');
        $this->addPublicProperty('callback');
        $this->addPublicProperty('lifetime');
        $this->addPublicProperty('name');
        $this->addPublicProperty('middleware', array());
        
        $this->method = $method;
        $this->path = $path;
        $this->callback = $callback;
        $this->lifetime = $lifetime;
    }
    
    public function hasParameters() {
        $count = 0;
    
        // Contains regular expressions to replace
        $regexp = array(
            '/:[a-zA-Z_][a-zA-Z0-9_]*/' => '[\w]+',
            '/\*/' => '.+'
        );

        // Prepare the string
        $path = str_replace('/', '\/', $this->path);
        
        // Replace the reqular expressions
        foreach ($regexp as $key => $value) {
            if (preg_match($key, $path)) {
                $count++;
            }
        }
        
        return ($count > 0) ? $count : FALSE;
    }
    
    public function generateUrl(Array $data = array()) {
        $count = 0;
        
        $regexp = array(
            '/:[a-zA-Z_][a-zA-Z0-9_]*/' => '[\w]+',
            '/\*/' => '.+'
        );

        // Prepare the string
        $path = $this->path;
        
        if (count($data) > 0) {
            // Replace the reqular expressions
            foreach ($regexp as $key => $value) {
                $path = preg_replace($key, $data[$count], $path);
            }
        }
        
        return $path;
    }
    
    protected function setName($value) {
        
        return $value;
    }
    
    protected function setMiddleware(Array $values) {
        foreach ($values as $middleware) {
            if (!is_callable($callback)) {
                throw new InvalidArgumentException('Middleware is not callable.');
            }
        }
        
        return $values;
    }
}