<?php

require_once __DIR__ . "/../bootstrap.php";

if (!isset($_GET["id"])) {
    header("Location: /");
}

$post = Registry::fetch("api")->get_post($_GET["id"]);
?>

<a href="/">Home</a>
<h1>Posted by <?php echo $post->user->name ?>.</h1>
<p><q><?php echo $post->message ?></q></p>
