<?php
require_once("php/page.class.php");
require_once("php/util.class.php");
require_once("php/articlecrud.class.php");

$page = new Page(3);
$article = new ArticleCRUD();

try {
	$title=(util::posted($_POST['articletitleedit'])?$_POST['articletitleedit']:"");
	$content=(util::posted($_POST['articletextedit'])?$_POST['articletextedit']:"");
    $aid=(util::posted($_POST['blogID'])?$_POST['blogID']:"");

	$result=$article->updateArticle($title, $content,$aid);
	if($result['update']==1) {
		echo "Article updated<br />";
	} else {
		echo "Update Failed:<br>";
		echo $result['messages'];
	}
	?><p><a href="admin.php">Back to Admin page</a></p><?php
} catch (Exception $e) {
	echo "Error : ", $e->getMessage();
}
?>