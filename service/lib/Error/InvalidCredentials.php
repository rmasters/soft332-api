<?php

namespace Error;

class InvalidCredentials extends Error
{
    public function __construct($message = null) {
        $this->code = 401;
        if (!isset($message)) {
            $message = "Your username and password did not match a user.";
        }
        parent::__construct($message);
    }
}
