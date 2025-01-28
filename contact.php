<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
$page = new Page(0);
$pagename = "Contact us";
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
<p>Contact info goes here</p>
<p>Phone: 12345 67890</p>
<p>Email: email@email.com</p>
<?php
$now=new DateTime();
?>
</main>
</body>
</html>