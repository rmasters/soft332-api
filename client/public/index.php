<?php
/**
 * Homepage
 */

require __DIR__ . "/../bootstrap.php";

?>
<h1>Mumblr</h1>
<p>Logged in as <?php echo Registry::fetch("api")->userInfo->name ?> <a href="/logout.php">Logout</a></p>

<form action="/new.php" method="post">
<p><textarea name="message"></textarea><input type="submit" value="Post"></p>
</form>

<?php foreach (Registry::fetch("api")->get_stream() as $post): ?>
<article>
<h1><?php echo $post->message ?></h1>
<p>By <?php echo $post->user->name ?></p>
<?php if ($post->user->id == Registry::fetch("api")->userInfo->id): ?>
<p><a href="/delete_post.php?id=<?php echo $post->id ?>">Delete</a></p>
<?php endif ?>
</article>
<?php endforeach ?>
