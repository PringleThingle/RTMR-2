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
<body class="loginbody">
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<img class="loginimg" src="assets/ratimg.png" alt="" height=768 width=768>  
<form class="loginform" method="post" action="processlogin.php">  
<label class="loginlabel" for="username">Username</label><input class="logininput" type="text" name="username" id="username" required/><br />
<label class="loginlabel" for="password">Password</label><input class="logininput" type="password" name="userpass" id="userpass" required /><br />
<button class = "loginbutton" type="submit">Login</button>
</form>
</body>
</html>