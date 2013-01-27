<?php

require_once __DIR__ . "/../bootstrap.php";

Registry::fetch("api")->logout();

header("Location: /");
