<?php

namespace Error;

class NotFound extends Error
{
    public function __construct($message = null) {
        $this->code = 404;
        if (!isset($message)) {
            $message = "The entity you requested does not exist";
        }
        parent::__construct($message);
    }
}
