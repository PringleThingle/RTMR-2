<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
$page = new Page(0);
$pagename = "Welcome";
view::showHead($pagename);
?>
</head>
<body>
<?php view::showHeader($pagename); ?>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<main>
<p>Welcome to the Local Theatre Company</p>
<?php
$now=new DateTime();
?>
</main>
</body>
</html>