<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<h1>Update Article</h1>
<?php
require_once("php/page.class.php");
require_once("php/user.class.php");
require_once("php/review.class.php");

$page = new Page(2);
$editreview = new Review();
$currentUser = $page->getUser();
#$edituser = new User();
$editid = isset($_GET['rid']) ? $_GET['rid'] : null;

$found=$editreview->getReviewById($editid);
?>
<nav>
<ul class="navbar">
<?php 
echo $page->getMenu(); 
?>
</ul>
</nav>
<?php
if($found) {
    if (($currentUser->getUserLevel() == 3) || ($currentUser->getUserid() == $editreview->getAuthor()->getUserid())) {
?>
<h1>Edit Review</h1>

<form method="post" action="updatereview.php">
<input type="hidden" name="rid" id="rid" value="<?php echo $editreview->getRID();?>" required readonly />
<label for="reviewtext">Review</label><textarea type="text" id="reviewtext" name="reviewtext" cols="60" rows="8" required><?php echo $editreview->getText();?></textarea><br />
<button type="submit">Update Review</button>
</form>
<?php
    } else {
        echo "<p>You do not have permission to edit this review.</p>";
    }

} else {
	echo "<p>Cannot find review to edit $editid</p>";
}
?>
</body>
</html>