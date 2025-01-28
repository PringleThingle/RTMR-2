<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<h1>Add Article</h1>
<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
$page = new Page(3);
?>
<nav><ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul></nav>

<main>
	<form method="post" action="aarticle.php">
	<fieldset><legend>Add Article</legend>
	<label for="title">Title </label><input type="text" name="title" id="title" required size="40" /><br />
	<label for="content">Content</label><textarea name="content" id="content" cols="60" rows="8" required></textarea><br />
	<button type="submit">Add Article</button>
	</fieldset>
	</form>

</main>
</body>
</html>
