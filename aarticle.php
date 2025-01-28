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
	$content=util::sanStr($_POST['content']);
	$articletoadd=new Article();
	$result=$articletoadd->addArticle($page->getUser()->getUserid(),$title,$content);
	if($result['insert']>0) {
		$found=$page->getLastUserArticle($page->getUser()->getUserid());
		if($found) {
			echo "<h2>Article Added</h2>";
			echo $page->displayArticles();
		}
	} else { 
		echo "<h2>Add Failed</h2>";
		echo $result['messages'];
	}
}
?>
</main>
</body>
</html>
