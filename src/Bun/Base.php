<?php

abstract class Base {
    private $properties = array();
    
    protected function addPublicProperty($name, $value = NULL) {
        $this->properties[$name] = $value;
    }
    
    public function __set($property, $value) {
        $methodname = 'set'.ucwords($property);

        if (array_key_exists($property, $this->properties)) {
            if (method_exists($this, $methodname)) {
                $this->properties[$property] = $this->$methodname($value);
            } else {
                $this->properties[$property] = $value;
            }
        } else {
            throw new Exception(sprintf('Property "%s" does not exist.', $property));
        }
    }
    
    public function __get($property) {
        $methodname = 'get'.ucwords($property);

        if (array_key_exists($property, $this->properties)) {
            if (method_exists($this, $methodname)) {
                return $this->$methodname();
            } else {
                return $this->properties[$property];
            }
        } else {
            throw new Exception(sprintf('Property "%s" does not exist.', $property));
        }
    }
}