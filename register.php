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
<form id="regform" method="post" action="reguser.php">
<label for="username">Username</label><input type="text" id="username" name="username" required /><br />
<label for="firstname">First name</label><input type="text" id="firstname" name="firstname" required /><br />
<label for="surname">Surname</label><input type="text" id="surname" name="surname" required /><br />
<label for="email">Email</label><input type="email" id="email" name="email" required /><br />
<label for="dob">DOB</label><input type="date" id="dob" name="dob" /><br />
<label for="userpass">Password</label><input type="password" id="userpass" name="userpass" required /><br />
<button id="submitbutton" name="submitbutton" type="submit">Register</button>
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