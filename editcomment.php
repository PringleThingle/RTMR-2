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
require_once("php/comment.class.php");

$page = new Page(2);
$editcomment = new Comment();
$currentUser = $page->getUser();
#$edituser = new User();
$editid = isset($_GET['cid']) ? $_GET['cid'] : null;

$found=$editcomment->getCommentById($editid);
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
    if (($currentUser->getUsertype() == 3) || ($currentUser->getUserid() == $editcomment->getAuthor()->getUserid())) {
?>
<h1>Update Comment</h1>

<form method="post" action="updatecomment.php">
<input type="hidden" name="commentID" id="commentID" value="<?php echo $editcomment->getCID();?>" required readonly />
<label for="commenttext">Comment</label><textarea type="text" id="commenttext" name="commenttext" cols="60" rows="8" required><?php echo $editcomment->getText();?></textarea><br />
<button type="submit">Update Comment</button>
</form>
<?php
    } else {
        echo "<p>You do not have permission to edit this comment.</p>";
    }

} else {
	echo "<p>Cannot find comment to edit $editid</p>";
}
?>
</body>
</html>