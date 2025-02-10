<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
$page = new Page(3);
?>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<h1>Edit User</h1>
<div>
<?php
try {
	if(util::valInt($_POST['userid'])) {$userid=$_POST['userid'];}
	else { $page->logout();}
	
	$usertype=(util::posted($_POST['usertype'])?$_POST['usertype']:"");
	$username=(util::posted($_POST['username'])?$_POST['username']:"");
	$email=(util::posted($_POST['email'])?$_POST['email']:"");
	$userpass=(util::posted($_POST['userpass'])?$_POST['userpass']:"");
	$result=$page->updateUser($username,$userpass,$email,$userid, $usertype);
	if($result['update']==1) {
		echo "User updated<br />";
	} else {
		echo "Update Failed:<br>";
		echo $result['messages'];
	}
	?><p><a href="admin.php">Back to Admin page</a></p><?php
} catch (Exception $e) {
	echo "Error : ", $e->getMessage();
}
?>
</div>
</body>
</html>