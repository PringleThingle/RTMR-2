<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<h1>Admin Panel</h1>
<?php
require_once("php/page.class.php");
require_once("php/userlist.class.php");
$page = new Page(3);
?>
<nav>
<ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul>
</nav>
<form method="post" action="editother.php">
<?php
$userlist = new UserList();
echo $userlist;
?>
<button id="edituserbutton" type="submit">Edit User</button>
</form>
</body>
</html>