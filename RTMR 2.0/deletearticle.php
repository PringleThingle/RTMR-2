<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
require_once("php/articlecrud.class.php");

$page = new Page(3);
$article = new ArticleCRUD();

try {
    $aid=(util::posted($_GET['aid'])?$_GET['aid']:"");

	$result=$article->deleteArticle($aid);
	if($result['update']==1) {
		echo "Article deleted<br />";
	} else {
		echo "Delete Failed:<br>";
		echo $result['messages'];
	}
	?><p><a href="admin.php">Back to Admin page</a></p><?php
} catch (Exception $e) {
	echo "Error : ", $e->getMessage();
}
?>