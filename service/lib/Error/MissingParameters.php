<?php

namespace Error;

class MissingParameters extends Error
{
    public function __construct($message = null) {
        $this->code = 406; // TODO: find correct code
        if (!isset($message)) {
            $message = "Your request is missing some required parameters.";
        }
        parent::__construct($message);
    }
}
