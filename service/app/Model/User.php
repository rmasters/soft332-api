<?php

namespace Model;
use \DateTime;

class User extends \Model
{
    protected $id;
    protected $name;
    protected $password;
    protected $registered;

    public function setId($id) {
        $this->id = (int) $id;
    }

    public function setName($name) {
        if (!filter_var($name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/[A-Za-z0-9_-]+/")))) {
            throw new Exception("Invalid name - can only contain letters, numbers, underscores and dashes.");
        }

        if (strlen($name) < 1 && strlen($name) > 50) {
            throw new Exception("Name must be between 1 and 50 characters");
        }

        $this->name = strtolower($name);
    }

    public function setRegistered($ts) {
        if (!($ts instanceof DateTime)) {
            $ts = new DateTime($ts);
        }
        $this->registered = $ts;
    }

    public function getRegistered() {
        if (!isset($this->registered)) {
            $this->registered = new DateTime("now");
        }
        return $this->registered;
    }

    public static function hashPassword($password) {
        return sha1($password . "asd9fh");
    }

    public function toCleanArray() {
        $vars = get_object_vars($this);
        unset($vars["password"]);
        return $vars;
    }
}
