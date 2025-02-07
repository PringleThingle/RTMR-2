<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<h1>Add Review</h1>
<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
$page = new Page(2);
?>
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
        <form method="post" action="areview.php">
        <fieldset><legend>Add Review</legend>
        <input type="hidden" id="mid" name="mid" value="<?php echo $mid; ?>" required readonly>
        <label for="reviewText">Review</label><textarea name="reviewText" id="reviewText" required cols="30" rows="4"></textarea><br />
        <button type="submit">Add Review</button>
        </fieldset>
        </form>
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
