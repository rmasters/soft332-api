<?php
/**
 * A container for relevant HTTP data from the current request
 */

class Request
{
    public $verb;
    private static $verbs = array("GET", "POST", "PUT", "DELETE", "HEAD");

    public $resource;
    public $version;

    public $params = array();

    public $session;
}
