<?php

require_once __DIR__ . "/../bootstrap.php";

if (isset($_POST["username"], $_POST["password"])) {
    $res = Registry::fetch("api")->new_user(array("name" => $_POST["username"], "password" => $_POST["password"]));
    var_dump($res);
}

?>

<form action="/register.php" method="post">
    <p>Username <input type="text" name="username"></p>
    <p>Password <input type="password" name="password"></p>
    <p><input type="submit" value="Register"></p>
</form>
