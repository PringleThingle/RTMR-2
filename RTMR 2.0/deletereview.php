<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
require_once("php/reviewcrud.class.php");

$page = new Page(2);
$review = new Review();
$currentUser = $page->getUser();

try {
    // Retrieve the Comment ID
    $rid = (util::posted($_GET['rid']) ? $_GET['rid'] : "");

    if (!$rid) {
        die("Error: No review ID provided.");
    }

    // Initialize the Comment with data from the database
    if ($review->getReviewById($rid)) {
        // Check user permissions AFTER the comment is loaded
        if (($currentUser->getUserLevel() == 3) || ($currentUser->getUserid() == $review->getAuthor()->getUserid())) {

            // Delete the Comment
            $result = $review->deleteReview($rid);
            echo "Review Deleted.";
        } else {
            echo "Error: Unauthorized access.";
        }
    } else {
        echo "Error: Review not found.";
    }
} catch (Exception $e) {
    echo "Error: ", $e->getMessage();
}
?>
<p><a href="home.php">Back to Home page</a></p>