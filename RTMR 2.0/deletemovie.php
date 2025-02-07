<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
require_once("php/moviecrud.class.php");

$page = new Page(3);
$article = new MovieCRUD();

try {
    $aid=(util::posted($_GET['mid'])?$_GET['mid']:"");

	$result=$article->deleteMovie($mid);
	if($result['update']==1) {
		echo "Movie deleted<br />";
	} else {
		echo "Delete Failed:<br>";
		echo $result['messages'];
	}
	?><p><a href="admin.php">Back to Admin page</a></p><?php
} catch (Exception $e) {
	echo "Error : ", $e->getMessage();
}
?>