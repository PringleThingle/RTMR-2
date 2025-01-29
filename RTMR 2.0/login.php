<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
$page = new Page();
$pagename = "Login";
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
<form method="post" action="processlogin.php">
<label for="username">Username</label><input type="text" name="username" id="username" /><br />
<label for="password">Password</label><input type="password" name="userpass" id="userpass" /><br />
<button type="submit">Login</button>
</form>
</body>
</html>