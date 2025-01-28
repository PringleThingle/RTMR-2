<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<h1>User Suspended</h1>
<?php
require_once("php/page.class.php");
$page = new Page(1);

?>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<?php
echo $page->getUser(); 
?>
</body>
</html>