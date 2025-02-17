<!doctype html>
<head>
<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
require_once("php/view.class.php");
$page = new Page(2);
$pagename="Add review";
view::showHead($pagename);
view::showHeader($pagename);
?>
</head>
<body>
<nav><ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul></nav>
<main>
<?php
if(util::posted($_GET['mid'])) {
    $mid=util::sanInt($_GET['mid']);
    $found=$page->getMovie($mid);
    if ($found) {
        ?>
        <form class="addreviewform" method="post" action="areview.php">
        <input type="hidden" id="mid" name="mid" value="<?php echo $mid; ?>" required readonly>
        <label class="addreviewlabel" for="reviewText">Review</label><textarea class="addreviewtext" name="reviewText" id="reviewText" required cols="30" rows="4"></textarea><br />
        <ul class="reviewRatingul">
        <li><input type="range" id="userRating" name="userRating" min="0" max="10" step="0.1" required></li>
        <li class="ratingli"><p class="ratingp"><output class="ratingoutput" id="value"></output></p></li>
        <li class="ratingli2"><p class="ratingp2">/10</p></li>
        </ul>
        <button class="menubutton" type="submit">Add Review</button>
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
        echo $page->displayMovies();
    } else {
        echo "<h2>Could not find movie</h2>";
    }
}
?>


</main>
</body>
</html>
