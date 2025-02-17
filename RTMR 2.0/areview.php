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
require_once("php/review.class.php");

$page = new Page(2);
$comment = new Review();
?>
<nav><ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul></nav>

<main>
<?php

if(util::posted($_POST['mid']) && util::posted($_POST['reviewText'])) {
	$mid=util::sanInt($_POST['mid']);
    $text=util::sanStr($_POST['reviewText']);
    $rating=util::sanFloat(isset($_POST['userRating']) ? floatval($_POST['userRating']) : 0.0);
	$reviewtoadd=new Review();
    $result = $reviewtoadd->addReview($page->getUser()->getUserid(), $text, $mid, $rating);

    if ($result['insert'] == 1) { 
        header("Location: home.php");
        // echo "<h2>Review Added</h2>";
        // echo $page->displayMovies();
    } else { 
        echo "<h2>Add Failed</h2>";
        if (is_array($result) && isset($result['messages'])) {
            echo $result['messages'];
        } else {
            echo "Unexpected error occurred.";
        }
    }
}

?>
</main>
</body>
</html>