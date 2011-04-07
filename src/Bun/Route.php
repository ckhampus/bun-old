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
        $count = count($this->getParameters());
        
        return ($count > 0) ? $count : FALSE;
    }
    
    public function getParameters() {
        $matches = array();
        $regexp = '/:[a-zA-Z_][a-zA-Z0-9_]*/';
        $count = preg_match_all($regexp, $this->path, $matches);
        
        return $matches[0];
    }
    
    /**
     * Matches the route with the current path. 
     * 
     * @param string $path 
     * @return bool
     */
    public function matchRoute($path)
    {
        // Matches the route with the current path
        return preg_match(sprintf('/^%s$/', $this->getRealPath()), $path);
    }
    
    public function getPathWithValues(Array $data = array()) {
        return $this->getRealPath($data);
    }
    
    protected function getRealPath(Array $values = array()) {
        if (empty($values)) {
            $path = str_replace('/', '\/', $this->path);
            $values = array_fill_keys($this->getParameters(), '[\w]+');
        } else {
        
            $path = $this->path;
            $values = array_combine($this->getParameters(), $values);
        }

        // Replace the reqular expressions
        foreach ($values as $key => $value) {
            $path = str_replace($key, $value, $path);
        }

        return $path;
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