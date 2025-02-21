<?php
require_once("page.class.php");
$page = new Page();

if (!isset($_POST['lastWatchedDate']) || empty($_POST['lastWatchedDate'])) {
    http_response_code(400);
    echo "Error: lastWatchedDate is required.";
    exit;
}

$lastWatchedDate = $_POST['lastWatchedDate'];

$movies = $page->getMovies($lastWatchedDate, 10, "PREV");  
if (!empty($movies)) {
    echo $page->displayMovies($movies);
} else {
    echo "";
}

