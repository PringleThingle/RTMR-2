<!doctype html>
<head>
<?php
require_once("php/page.class.php");
$page = new Page(2);
require_once("php/view.class.php");
$pagename = "User home";
view::showHead($pagename);
view::showHeader($pagename);
?>
</head>
<body>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<div>
<?php
echo $page->getUser(); 
?>
</div>
<p><a href="editself.php">Edit Details</a></p>
</body>
</html>