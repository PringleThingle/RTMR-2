<!doctype html>
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="styles/style.css" />
</head>
<body>
<h1>Add Comment</h1>
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
if(util::posted($_GET['aid'])) {
    $aid=util::sanInt($_GET['aid']);
    $found=$page->getArticle($aid);
    if ($found) {
        ?>
        <form method="post" action="acomment.php">
        <fieldset><legend>Add Comment</legend>
        <input type="hidden" id="aid" name="aid" value="<?php echo $aid; ?>" required readonly>
        <label for="commenttext">Comment</label><textarea name="commenttext" id="commenttext" required cols="30" rows="4"></textarea><br />
        <button type="submit">Add Comment</button>
        </fieldset>
        </form>
        <?php
        echo $page->displayArticles();
    } else {
        echo "<h2>Could not find article</h2>";
    }
}
?>


</main>
</body>
</html>
