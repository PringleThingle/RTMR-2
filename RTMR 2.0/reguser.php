<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/view.class.php");
$page = new Page(0);
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
<?php
require_once("php/user.class.php");

$username=$_POST['username'];
$email=$_POST['email'];
$userpass=$_POST['userpass'];
$reguser=new User();

if (strlen($email) > 45) {
	die("Email must not exceed 45 characters.");
} else {
	try {
		$result=$reguser->registerUser($username,$userpass,$email);
		if($result['insert']==1) {
			echo "User Registered<br />";
			?>
			<form method="post" action="processlogin.php">
				<label for="username">Username</label><input type="text" name="username" id="username" <?php echo "value='",$username,"' "; ?>/><br />
				<label for="password">Password</label><input type="password" name="userpass" id="userpass" /><br />
				<button type="submit">Login</button>
			</form>
			<?php
			echo "Re-enter your password to login<br/>";
		}	else {
			echo $result['messages'];
			?><a href="javascript:history.back(-1);">Back to Registration Form</a><?php
		}
		
	} catch (Exception $e) {
		echo "Error : ", $e->getMessage();
	}
}
?>
</body>
</html>