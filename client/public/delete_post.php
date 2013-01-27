<?php

require_once __DIR__ . "/../bootstrap.php";

if (!isset($_GET["id"])) {
    header("Location: /");
}

$res = Registry::fetch("api")->delete_post($_GET["id"]);

header("Location: /");
?>
