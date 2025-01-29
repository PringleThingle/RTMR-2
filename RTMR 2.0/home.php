<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
$page = new Page();
$pagename = "Articles";
view::showHead($pagename);
view::showHeader($pagename);
?>
<body>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<main>
<?php
$now=new DateTime();
$page->getArticles($now->format("Y-m-d H:i:s O"), 2, "DESC");
echo $page->displayArticles();
?>
</main>



</body>
<script src="js/touch.js"></script>
<script src="js/article.js"></script>
<script hidden=True>
document.onreadystatechange = function(){
	if(document.readyState=="complete") {
		var articlehandler=new Article("main");
		var mytouchhandler=new TouchScaler(["#mainheader","nav","main"]);
	}
}
</script>
</html>