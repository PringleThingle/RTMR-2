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
<div class="userinfo">
<h2 class="usertitle">User information</h2>
<?php
echo $page->getUser(); 
?>
</div>
<button class="edituserbutton"><a class="menubuttontext" href="editself.php">Edit Details</a></button>
</body>
</html>