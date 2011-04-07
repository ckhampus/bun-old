<?php

/**
 * Base
 *
 * This is the base class all other classes extend.
 * 
 * @package Core
 */
abstract class Base {
    private $properties = array();
    
    /**
     * Add a public property to the class. 
     * 
     * @param string $name 
     * @param mixed $value 
     * @return void
     */
    protected function addPublicProperty($name, $value = NULL) {
        $this->properties[$name] = $value;
    }
    
    /**
     * Magic setter function for properties. 
     * 
     * @param mixed $property 
     * @param mixed $value 
     * @return void
     */
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
    
    /**
     * Magic getter function for properties. 
     * 
     * @param mixed $property 
     * @return mixed
     */
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
