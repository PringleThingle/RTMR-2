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
	<form method="post" action="amovie.php">
	<fieldset><legend>Add Movie</legend>
	<label for="mid">Movie ID </label><input type="int" name="mid" id="mid" required size="8" /><br />
	<label for="title">Title </label><input type="text" name="title" id="title" required size="40" /><br />
	<label for="description">Description</label><textarea name="description" id="description" cols="60" rows="8" required></textarea><br />
	<label for="posterLink">Poster Link</label><input name="posterLink" id="posterLink" required size = "8"/></input><br />
	<label for="director">Director ID</label><input name="director" id="director" required size = "8"/></input><br />
	<button type="submit">Add Article</button>
	</fieldset>
	</form>

</main>
</body>
</html>
