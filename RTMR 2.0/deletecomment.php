<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
require_once("php/commentcrud.class.php");

$page = new Page(2);
$comment = new Comment();
$currentUser = $page->getUser();

try {
    // Retrieve the Comment ID
    $cid = (util::posted($_GET['cid']) ? $_GET['cid'] : "");

    if (!$cid) {
        die("Error: No comment ID provided.");
    }

    // Initialize the Comment with data from the database
    if ($comment->getCommentById($cid)) {
        // Check user permissions AFTER the comment is loaded
        if (($currentUser->getUsertype() == 3) || ($currentUser->getUserid() == $comment->getAuthor()->getUserid())) {

            // Delete the Comment
            $result = $comment->deleteComment($cid);
            echo "Comment Deleted.";
        } else {
            echo "Error: Unauthorized access.";
        }
    } else {
        echo "Error: Comment not found.";
    }
} catch (Exception $e) {
    echo "Error: ", $e->getMessage();
}
?>
<p><a href="home.php">Back to Home page</a></p>