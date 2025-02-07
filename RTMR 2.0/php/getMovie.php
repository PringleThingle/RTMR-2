<?php
require_once("page.class.php");
$page = new Page();

if (!isset($_POST['movieDate'])) {
    http_response_code(400);
    echo "Error: blogpostdate is required.";
    exit;
}

$movieDate = $_POST['movieDate'];

// Fetch articles and render them using displayArticles
if ($page->getMovies($movieDate, 1, "PREV") > 0) {
    echo $page->displayMovies();
} else {
    echo "<p>No more articles found.</p>"; // Return fallback HTML
}
