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
<?php
if(util::posted($_POST['title']) && util::posted($_POST['content'])) {
	$title=util::sanStr($_POST['title']);
	$content=util::sanStr($_POST['description']);
	$movietoadd=new Movie();
	$result=$movietoadd->addMovie($mid,$title,$description,$posterLink,$director);
	if($result['insert']>0) {
		echo "<h2>Movie Added</h2>";
		echo $page->displayMovies();
	} else { 
		echo "<h2>Add Failed</h2>";
		echo $result['messages'];
	}
}
?>
</main>
</body>
</html>
