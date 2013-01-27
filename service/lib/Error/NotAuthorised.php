<?php

namespace Error;

class NotAuthorised extends Error
{
    public function __construct($message = null) {
        $this->code = 401;
        if (!isset($message)) {
            $message = "You are not authorised to access this entity";
        }
        parent::__construct($message);
    }
}
