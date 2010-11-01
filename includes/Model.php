<?php

class Model
{
    protected static $db;
    
    public static function setDb(PDO $db) {
        self::$db = $db;
    }
    
    public static function getDb() {
        return self::$db;
    }
    
    public function __call($func, $args) {
        $property = substr($func, 3);
        $action = substr($func, 0, 3);
        
        if ($action == "set") {
            $this->set($property, $args[0]);
        } elseif ($action == "get") {
            return $this->get($property);
        } else {
            throw new Exception("Undefined method.");
        }
    }
    
    public function __set($name, $value) {
        $this->set($name, $value);
    }
    
    protected function set($name, $value) {
        $name = strtolower($name);
        $method = "set" . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        } elseif (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new Exception("Cannot set invalid property $name.");
        }
    }
    
    public function __get($name) {
        return $this->get($name);
    }
    
    protected function get($name) {
        $name = strtolower($name);
        $method = "get" . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new Exception("Cannot get invalid property $name.");
        }
    }
    
    public function __isset($name) {
        return $this->check($name);
    }
    
    protected function check($name) {
        $method = "get" . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method() == null;
        } elseif (property_exists($this, $name)) {
            return isset($this->$name);
        } else {
            throw new Exception("Cannot check invalid property $name.");
        }
    }
    
    public function __unset($name) {
        $this->delete($name);
    }
    
    protected function remove($name) {
        $method = "set" . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method(null);
        } elseif (property_exists($this, $name)) {
            unset($this->$name);
        } else {
            throw new Exception("Cannot unset invalid property $name.");
        }
    }
    
    public function __construct(array $options = null) {
        if ($options) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions(array $options) {
        foreach ($options as $key => $value) {
            $this->set($key, $value);
        }
    }
}
