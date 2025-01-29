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
<?php
try {
	if(util::valInt($_POST['userid'])) {$userid=$_POST['userid'];}
	else { $page->logout();}
	$usertype=$page->getUser()->getUsertype();
	$username=(util::posted($_POST['username'])?$_POST['username']:"");
	$firstname=(util::posted($_POST['firstname'])?$_POST['firstname']:"");
	$surname=(util::posted($_POST['surname'])?$_POST['surname']:"");
	$email=(util::posted($_POST['email'])?$_POST['email']:"");
	$dob=(util::posted($_POST['dob'])?$_POST['dob']:"");
	$userpass=(util::posted($_POST['userpass'])?$_POST['userpass']:"");
	
	$result=$page->updateUser($username,$firstname,$surname,$userpass,$email,$dob,$userid, $usertype);
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
</body>
</html>