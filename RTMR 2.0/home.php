<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
$page = new Page();
$pagename = "Movie Reviews";
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
$page->getMovies($now->format("Y-m-d H:i:s O"), 2, "DESC");
echo $page->displayMovies();
?>
</main>

</body>
<script src="js/touch.js"></script>
<script src="js/movies.js"></script>
<script hidden>
document.onreadystatechange = function(){
    if(document.readyState == "complete") {
        new MovieLoader("#main");
        var mytouchhandler = new TouchScaler(["#mainheader", "nav", "main"]);
    }
}
</script>
</html>