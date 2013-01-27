<?php
/**
 * Bootstrapper
 */

define("ROOT", __DIR__ . "/..");
define("APP", __DIR__);
define("LIB", ROOT . "/lib");

define("LATEST_VERSION", 1);

// Redirect PHP user errors to throw ErrorExceptions instead
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Add the current directory to the include path
set_include_path(implode(PATH_SEPARATOR, array(
    APP, LIB, get_include_path()
)));

// Class autoloader
spl_autoload_register(function ($name) {
    $file = str_replace(array("\\", "_"), "/", $name) . ".php";

    // Check if the file exists within the include_path
    if (!stream_resolve_include_path($file)) {
        throw new Exception("Cannot autoload $name as $file does not exist.");
    }

    require_once $file;

    if (!class_exists($name, false)) {
        throw new Exception("$name not defined in $file.");
    }
});

// Setup database connection
$db = new PDO("mysql:host=localhost;dbname=mumblr", "mumblr", "password");
Mapper::setDb($db);

// Setup session length
Model\Session::$defaultTTL = new DateInterval("P1W");
