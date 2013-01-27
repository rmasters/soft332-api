<?php

class Registry
{
    protected $variables = array();
    private static $instance;

    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
    }

    public function __set($key, $value) {
        $this->variables[$key] = $value;
    }

    public static function store($key, $value) {
        self::getInstance()->$key = $value;
    }

    public function __get($key) {
        return $this->variables[$key];
    }

    public static function fetch($key) {
        return self::getInstance()->$key;
    }

    public function __isset($key) {
        return isset($this->variables[$key]);
    }

    public static function exists($key) {
        return isset(self::getInstance()->$key);
    }

    public function __unset($key) {
        unset($this->variables[$key]);
    }

    public static function delete($key) {
        unset(self::getInstance()->$key);
    }
}

