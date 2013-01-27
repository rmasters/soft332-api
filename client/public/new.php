<?php

require_once __DIR__ . "/../bootstrap.php";

if (isset($_POST["message"])) {
    $res = Registry::fetch("api")->new_post($_POST["message"]);
    header("Location: /");
}
