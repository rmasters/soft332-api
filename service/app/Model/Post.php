<?php

namespace Model;
use \DateTime;

class Post extends \Model
{
    protected $id;
    protected $user;
    protected $message;
    protected $posted;

    public function setId($id) {
        $this->id = (int) $id;
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

    public function setPosted($ts) {
        if (!($ts instanceof DateTime)) {
            $ts = new DateTime($ts);
        }
        $this->posted = $ts;
    }

    public function getPosted() {
        if (!isset($this->posted)) {
            $this->posted = new DateTime("now");
        }
        return $this->posted;
    }
}
