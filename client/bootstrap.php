<?php
/**
 * Bootstrapper
 */

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__, __DIR__ . "/lib", get_include_path()
)));
spl_autoload_register(function ($name) {
    require str_replace(array("\\", "_"), "/", $name) . ".php";
});

session_start();

Registry::store("api", new Mumblr);

if (!in_array($_SERVER["PHP_SELF"], array("/login.php", "/register.php"))) {
    if (!Registry::fetch("api")->authenticated()) {
        header("Location: /login.php");
        echo 'You are not logged in: <a href="/login.php">login</a>.';
        exit;
    }
}
