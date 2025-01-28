<?php
require_once("page.class.php");
$page = new Page();

if (!isset($_POST['blogpostdate'])) {
    http_response_code(400);
    echo "Error: blogpostdate is required.";
    exit;
}

$blogpostdate = $_POST['blogpostdate'];

// Fetch articles and render them using displayArticles
if ($page->getArticles($blogpostdate, 1, "PREV") > 0) {
    echo $page->displayArticles();
} else {
    echo "<p>No more articles found.</p>"; // Return fallback HTML
}
