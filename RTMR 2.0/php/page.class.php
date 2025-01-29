<?php
require_once("user.class.php");
require_once("menu.class.php");
require_once("article.class.php");
require_once("comment.class.php");

class Page {
	private $user, $pagetype, $isauthenticated, $menu;
	private $articles=[];
	private $comments=[];

	/**************
	* Creates a new page
	* initialised with a page type, user, status, check to make sure a user doesnt exist, and a menu
	**************/
	public function __construct($pagetype=0){
		session_start();
		$this->setPagetype($pagetype);
		$this->user = new User();
		$this->setStatus(false);
		$this->checkUser();
		$this->menu = new Menu($this->getUser()->getUsertype());
	}
	
	/**************
	* Class getters
	**************/
	public function getMenu() { return $this->menu;}
	public function getPagetype() { return $this->pagetype;}
	public function getStatus() { return $this->isauthenticated;}
	public function getUser() {return $this->user;}
	

	/**************
	* Class setters
	**************/
	private function setPagetype($pagetype) {$this->pagetype=(int)$pagetype;}
	private function setStatus($status) {$this->isauthenticated=(bool)$status;}
	
	/**************
	* Checks if the current user has a userid and authorises them.
	* If the current user does not have a userid then they are logged out.
	**************/
	public function checkUser() {
		if(isset($_SESSION['userid']) && $_SESSION['userid']!="") {
			$this->setStatus($this->getUser()->authIdSession($_SESSION['userid'],session_id()));
		}
		if((!$this->getStatus() && $this->getPagetype()>0) || ($this->getStatus() && $this->getUser()->getUsertype()<$this->getPagetype())) {
			$this->logout();
		}
	}

	/**************
	* Logs a user in after checking their username and password match entries stored in the database.
	* Gives them a new session and stores it in the database
	**************/
	public function login($username, $userpass) {
		session_regenerate_id();
		if($this->getUser()->authNamePass($username,$userpass)) {
			echo "<br />Authenticated";
			$this->getUser()->storeSession($this->getUser()->getUserid(),session_id());
			$_SESSION['userid']=$this->getUser()->getUserid();

			switch($this->getUser()->getUsertype()) {
				case 1:
					header("location: suspended.php");
					break;
				case 2:
					header("location: user.php");
					break;
				case 3:
					header("location: admin.php");
					break;
			}
			exit();
			
		} else {
			echo "<br />Authentication failed";
		}
		
	}
	

	/**************
	* Logs a user out and destroys their session. Then redirects to login
	**************/
	public function logout() {
		if(isset($_SESSION['userid']) && $_SESSION['userid']!="") {
			$this->getUser()->storeSession($_SESSION['userid']);
		}
		session_regenerate_id();
		session_unset();
		session_destroy();
		header("location: login.php");
		exit();
	}


	/**************
	* Calls the user class and userCRUD class to update a users information
	**************/
	public function updateUser($username,$firstname,$surname,$password,$email,$dob,$userid, $usertype) {
		if($this->getUser()->getUsertype()==3 || $this->getUser()->getUserid()==$userid) {
			$usertoupdate=new User();
			$usertoupdate->getUserById($userid);
			if($this->getUser()->getUsertype()!=3) {
				$usertype="";
			}
			$result=$usertoupdate->updateUser($username,$firstname,$surname,$password,$email,$dob,$usertype, $userid);
			return $result;
			
		}
	}	

	/************
	* Empty article array
	************/
	public function clearArticles() { $this->articles=[];}

	/************
	* Return all articles in article array
	************/
	public function getArticleList() { return $this->articles;}
	
	/************
	* Uses ArticleCRUD class to retrieve an associative array of articles
	* associative array Uses Article class to create an array of instances
	* of Article, stored in the article array
	* accepts 3 parameters
	* @param start start date of query as a string
	* @param qty maximum number of articles to return
	* @param direction default DESC, order to retrieve articles
	* @return havearticles 0 if no articles found, >0 if articles found
	************/	
	public function getArticles($start,$qty,$direction='DESC') {
		$havearticles=0;
		$source=new ArticleCRUD();
		$articles=$source->getArticles($start,$qty,$direction);
		if(count($articles)>0) {
			$havearticles=count($articles);
			foreach($articles as $article) {
				$newarticle=new Article();
				$newarticle->initArticle($article);
				array_push($this->articles, $newarticle);
			}
		}
		return $havearticles;
	}
	/*************
	* Returns the last article by a $poster
	**************/
	public function getLastUserArticle($poster) {
		$article=new Article();
		if($article->getLastUserArticle($poster)) {
			array_push($this->articles,$article);
			return true;
		} else {return false;}
	}

	/*************
	* returns an article from an article id ($aid)
	**************/

	public function getArticle($aid) {
		$source=new Article();
		if($article=$source->getArticleByID($aid)) {
			array_push($this->articles,$article);
			return true;
		} else {return false;}
	}
	
	/*************
	* creates and returns a string from the articles stored
	* creates menus based upon the currently logged in user
	**************/

	public function displayArticles() {
		$output="";
		
		foreach($this->articles as $article) {
			$output.="<article>";
			$output.="<h1 id='articletitle'class='articletitle'>".htmlentities($article->getTitle())."</h1><div id='a".htmlentities($article->getID())."'>";
			
			$output.="<footer><h2>Written by ".htmlentities($article->getAuthor()->getUsername())."</h2><p> on <time datetime='".htmlentities($article->getADate()->format("Y-m-d H:i:s"))."'>".htmlentities($article->getADate()->format("Y-m-d"))." at ".htmlentities($article->getADate()->format("H:i:s"))."</time></p></footer>";
			$output.="<p id=\"articletext\">".nl2br(htmlentities($article->getContent()))."</p>";
			if($this->getStatus() && $this->getUser()->getUsertype()>=2) {
				$output.="<ul class='articlemenu'>";
			}
			if($this->getStatus() && $this->getUser()->getUsertype()>=3) {
				$output.="<li><a href='editarticle.php?aid=".$article->getID()."'>Edit Article</a></li>";
				$output.="<li><a href='deletearticle.php?aid=".$article->getID()."' onclick='return confirm(\"Do you want to delete this article?\");'>Delete Article</a></li>";
			}
			if($this->getStatus() && $this->getUser()->getUsertype()>=2) {
				$output.="<li><a href='addcomment.php?aid=".$article->getID()."'>Add Comment</a></li>";
				$output.="</ul>";
			
			}



			$output.="</div>";
			$output.="<section id='comments' class='comments'><h2>Comments</h2>";

			$comments = $article->getCommentsForArticle($article->getID());
			if (!empty($comments)) {
				foreach ($comments as $comment) {
					$output.="<div class='comment' solid #ccc; padding: 10px;'>";
					$output.="<footer><h3>Written by ".htmlentities($comment->getAuthor()->getUsername())."</h3><p> on <time datetime='".htmlentities($comment->getCDate()->format("Y-m-d H:i:s"))."'>".htmlentities($comment->getCDate()->format("Y-m-d"))." at ".htmlentities($comment->getCDate()->format("H:i:s"))."</time></p></footer>";
					$output.= "<p class='commenttext'>" . nl2br(htmlentities($comment->getText())) . "</p>";
					$output.="</div>";

					if(($this->getStatus() && $this->getUser()->getUsertype()>=3) || ($this->getStatus() && $this->getUser()->getUserid() == $comment->getAuthor()->getUserid())) {
						$output.="<li><a href='editcomment.php?cid=".$comment->getCID()."'>Edit</a></li>";
						$output.="</ul>";
					}

					if(($this->getStatus() && $this->getUser()->getUsertype()>=3) || ($this->getStatus() && $this->getUser()->getUserid() == $comment->getAuthor()->getUserid())) {
						$output.="<li><a href='deletecomment.php?cid=".$comment->getCID()."' onclick='return confirm(\"Do you want to delete this comment?\");'>Delete</a></li>";
						$output.="</ul>";
					
					}
				}
			} else {
				$output .= "<p>No comments yet. Be the first to comment!</p>";
			}
			$output.="</section></article>";
		}
		return $output;
	}

	
	/*************
	* creates and returns an array of information for each article including their comments
	**************/
	public function articlesToArray() {
		$output=[];
		$edit=false;
		$delete=false;
		$add=false;
		foreach($this->articles as $article) {
			$commentsarray=[];
			$comments = $article->getCommentsForArticle($article->getID());
			foreach($comments as $comment) {
				$cedit=false;
				$cdelete=false;
				$commentarray=$comment->toArray();

				if($this->getStatus() && ($this->getUser()->getUsertype()>=3 || ($this->getUser()->getUsertype()>=2 && $this->getUser()->getUserid()== $comment->getCommentor()->getUserid()))) {
					$cedit=true;
					$cdelete=true;	
				}

				$commentarray['edit']=$cedit;
				$commentarray['delete']=$cdelete;
				array_push($commentsarray,$commentarray);
			}
			if($this->getStatus() && $this->getUser()->getUsertype()>=3) {
				$edit=true;
				$delete=true;
			}
			if($this->getStatus() && $this->getUser()->getUsertype()>=2) {
				$add=true;
			
			}
			$articlearray=$article->toArray();
			$articlearray['add'] = $add;
			$articlearray['edit'] = $edit;
			$articlearray['delete'] = $delete;
			$articlearray['comments'] = $commentsarray;
			array_push($output,$articlearray);			
		}
		return $output;
	}


}
?>