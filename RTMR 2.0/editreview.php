<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
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

<form class="addreviewform" method="post" action="updatereview.php">
<input type="hidden" name="rid" id="rid" value="<?php echo $editreview->getRID();?>" required readonly />
<label class="addreviewlabel" for="reviewtext">Review</label><textarea class="addreviewtext" type="text" id="reviewtext" name="reviewtext" cols="60" rows="8" required><?php echo $editreview->getText();?></textarea><br />
<ul class="reviewRatingul">
    <li><input type="range" id="userRating" name="userRating" min="0" max="10" step="0.1" value="<?php echo isset($editreview) ? $editreview->getRating() : '5.0'; ?>" required></li>
    <li class="ratingli"><p class="ratingp"><output class="ratingoutput" id="value"></output></p></li>
    <li class="ratingli2"><p class="ratingp2">/10</p></li>
</ul>
<button class="menubutton" type="submit">Update Review</button>
</form>
<script>
    const value = document.querySelector("#value");
    const input = document.querySelector("#userRating");
    value.textContent = input.value;
    input.addEventListener("input", (event) => {
    value.textContent = event.target.value;
    });
</script>
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