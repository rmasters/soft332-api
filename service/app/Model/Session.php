<?php

namespace Model;
use \DateTime;

class Session extends \Model
{
    protected $user;
    protected $token;
    protected $created;
    protected $expires;

    public static $defaultTTL;

    public static function generateToken($length = 128) {
        $charset = "ABCDEFGHIJKLMNOPQRSTUVQXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $token = "";
        for ($i = 0; $i < $length; $i++) {
            $token .= $charset[mt_rand(1, strlen($charset))-1];
        }

        return $token;
    }

    public function getUser() {
        if (isset($this->user) && !($this->user instanceof User)) {
            $userMapper = new Mapper\User;
            $this->user = $userMapper->find($this->user);

            // If the user is invalid, set it to null
            if (!$this->user) {
                $this->user = null;
            }
        }
        return $this->user;
    }

    public function setCreated($ts) {
        if (!($ts instanceof DateTime)) {
            $ts = new DateTime($ts);
        }
        $this->created = $ts;
    }

    public function getCreated() {
        if (!isset($this->created)) {
            $this->created = new DateTime("now");
        }
        return $this->created;
    }

    public function setExpires($ts) {
        if (!($ts instanceof DateTime)) {
            $ts = new DateTime($ts);
        }
        $this->expires = $ts;
    }

    public function getExpires() {
        if (!isset($this->expires) && isset(self::$defaultTTL)) {
            $this->expires = $this->getCreated()->add(self::$defaultTTL);
        }
        return $this->expires;
    }

    public function expired() {
        return $this->expires->diff(new DateTime("now"))->invert == 0;
    }
}
