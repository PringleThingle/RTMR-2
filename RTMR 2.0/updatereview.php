<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
require_once("php/reviewcrud.class.php");

$page = new Page(2);
$review = new Review();
$currentUser = $page->getUser();

try {
    // Retrieve the Comment ID
    $rid = (util::posted($_POST['rid']) ? $_POST['rid'] : "");

    if (!$rid) {
        die("Error: No review ID provided.");
    }

    // Initialize the Comment with data from the database
    if ($review->getReviewById($rid)) {
        // Check user permissions AFTER the comment is loaded
        if (($currentUser->getUserLevel() == 3) || ($currentUser->getUserid() == $review->getAuthor()->getUserid())) {
            
            // Retrieve the updated text
            $text = (util::posted($_POST['reviewtext']) ? $_POST['reviewtext'] : "");

            $rating=util::sanFloat(isset($_POST['userRating']) ? floatval($_POST['userRating']) : 0.0);

            // Update the Comment
            $result = $review->updateReview($text, $rid, $rating);

            if ($result['update'] == 1) {
                header("Location: home.php");
                echo "Review updated<br />";
            } else {
                echo "Update Failed: " . $result['messages'] . "<br>";
            }
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
