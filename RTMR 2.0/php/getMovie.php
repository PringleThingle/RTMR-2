<?php
require_once("page.class.php");
$page = new Page();

if (!isset($_POST['lastWatchedDate']) || empty($_POST['lastWatchedDate'])) {
    http_response_code(400);
    echo "Error: lastWatchedDate is required.";
    exit;
}

$lastWatchedDate = $_POST['lastWatchedDate'];
error_log("Received lastWatchedDate: $lastWatchedDate");  // Log the received date for debugging

// Fetch and display movies with a watchedDate less than the lastWatchedDate
$movies = $page->getMovies($lastWatchedDate, 10, "PREV");  // Fetch the next 10 older movies
if (!empty($movies)) {
    echo $page->displayMovies($movies);
} else {
    echo "";
}

