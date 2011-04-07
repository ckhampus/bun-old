<?php

require('Base.php');

class Route extends Base {
    public $method;
    public $path;
    public $callback;
    public $lifetime;
    
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
    
    public function name($name) {
        $this->name = $name;
        
        return $this;
    }
    
    public function middleware() {
        $this->middleware = func_get_args();
        
        return $this;
    }
    
    private function setName($value) {
        $GLOBALS[$value] = $this;
        
        return $value;
    }
    
    private function setMiddleware(Array $values) {
        foreach ($values as $middleware) {
            if (!is_callable($callback)) {
                throw new InvalidArgumentException('Middleware is not callable.');
            }
        }
        
        return $values;
    }
}