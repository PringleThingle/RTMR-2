<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
$page = new Page(2);
$pagename = "Edit Details";
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
<form method="post" action="updateself.php">
<input type="hidden" name="userid" id="userid" value="<?php echo $page->getUser()->getUserid();?>" required readonly />
<label class="loginlabel" for="username">Username</label><input class="logininput" type="text" id="username" name="username" value="<?php echo $page->getUser()->getUsername();?>" required /><br />
<label class="loginlabel" for="email">Email</label><input class="edituserinput" type="email" id="email" name="email" value="<?php echo $page->getUser()->getEmail();?>" required /><br />
<label class="loginlabel" for="userpass">Password</label><input class="logininput" type="password" id="userpass" name="userpass" /><br />
<button class="edituserbutton" type="submit">Update Details</button>
</form>
</body>
</html>