<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<h1>Update Article</h1>
<?php
require_once("php/page.class.php");
require_once("php/user.class.php");
require_once("php/article.class.php");

$page = new Page(3);
$editarticle = new Article();
#$edituser = new User();
$editid = isset($_GET['aid']) ? $_GET['aid'] : null;
$found=$editarticle->getArticleById($editid);

?>
<nav>
<ul class="navbar">
<?php 
echo $page->getMenu(); 
?>
</ul>
</nav>
<?php
if($found) {
?>
<form method="post" action="updatearticle.php">
<input type="hidden" name="blogID" id="blogID" value="<?php echo $editarticle->getID();?>" required readonly />

<label for="articletitle">Article Title</label><input type="text" id="articletitleedit" name="articletitleedit" value="<?php echo $editarticle->getTitle();?>" required /><br />
<label for="articletext">Text</label><textarea type="text" id="articletextedit" name="articletextedit" cols="60" rows="8" required><?php echo $editarticle->getContent();?></textarea><br />
<button type="submit">Update Article</button>
</form>
<?php
} else {
	echo "<p>Cannot find article to edit $editid</p>";
}
?>
</body>
</html>