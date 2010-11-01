<?php

class Template
{
    protected $path;
    protected $variables = array();
    protected $contents;

    public function __construct($path) {
        $this->path = $path;
    }

    public function __get($name) {
        return $this->variables[$name];
    }

    public function __set($name, $value) {
        $this->variables[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->variables[$name]);
    }

    public function __unset($name) {
        unset($this->variables[$name]);
    }

    public function __toString() {
        require $this->path;
        $this->contents = ob_get_contents();
        ob_clean();
        if (empty($this->contents)) {
            return "";
        }
        return $this->contents;
    }
}
