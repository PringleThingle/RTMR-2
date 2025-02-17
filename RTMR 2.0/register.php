<!doctype html>
<head>
<?php
require_once("php/view.class.php");
require_once("php/page.class.php");
$page = new Page();
$pagename = "Register";
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
<img class="loginimg" src="assets/ratimg.png" alt="" height=768 width=768>  
<form class="loginform" id="regform" method="post" action="reguser.php">
<label class="loginlabel" for="username">Username</label><input class="logininput" type="text" id="username" name="username" required /><br />
<label class="loginlabel" for="email">Email</label><input class="logininput" type="email" id="email" name="email" required /><br />
<label class="loginlabel" for="userpass">Password</label><input class="logininput" type="password" id="userpass" name="userpass" required /><br />
<button class="loginbutton" id="submitbutton" name="submitbutton" type="submit">Register</button>
</form>
</body>
<script src="js/userform.js"></script>
<script>
document.onreadystatechange = function(){
	if(document.readyState=="complete") {
		var myform=new Form("regform",true);
	}
}
</script>
</html>