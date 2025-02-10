<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<?php
require_once("php/page.class.php");
$page = new Page(2);
?>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<h1>Edit Details</h1>
<div>
<?php
try {
	if(util::valInt($_POST['userid'])) {$userid=$_POST['userid'];}
	else { $page->logout();}
	$usertype=$page->getUser()->getUserLevel();
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
	?><p><a href="user.php">Back to User page</a></p><?php

} catch (Exception $e) {
	echo "Error : ", $e->getMessage();
}
?>
</div>
</body>
</html>