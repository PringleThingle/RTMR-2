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
require_once("php/comment.class.php");

$page = new Page(2);
$comment = new Comment();
?>
<nav><ul class="navbar">
<?php echo $page->getMenu(); ?>
</ul></nav>

<main>
<?php

if(util::posted($_POST['aid']) && util::posted($_POST['commenttext'])) {
	$aid=util::sanInt($_POST['aid']);
    $text=util::sanStr($_POST['commenttext']);
	$commenttoadd=new Comment();
    $result = $commenttoadd->addComment($page->getUser()->getUserid(), $text, $aid);

    if ($result['insert'] == 1) { 
        echo "<h2>Comment Added</h2>";
        echo $page->displayArticles();
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