<?php

require_once __DIR__ . "/../bootstrap.php";

if (isset($_POST["username"], $_POST["password"])) {
    $res = Registry::fetch("api")->login($_POST["username"], $_POST["password"]);

    if (isset(Registry::fetch("api")->userInfo)) {
        header("Location: /");
        echo 'Logged in, <a href="/">continue</a>';
        exit;
    }
}

?>
<form action="/login.php" method="post">
    <p>Username <input type="text" name="username"></p>
    <p>Password <input type="password" name="password"></p>
    <p><input type="submit" value="Login"></p>
</form>

<form action="/register.php" method="post">
    <p>Username <input type="text" name="username"></p>
    <p>Password <input type="password" name="password"></p>
    <p><input type="submit" value="Register"></p>
</form>
