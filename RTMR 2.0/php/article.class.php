<?php

require_once("articlecrud.class.php");
require_once("user.class.php");
//require_once("comment.class.php");
require_once("util.class.php");


/**************
* Article class
**************/
class Article {
	/***************
	* aid - serial article id
	* adate - instance of DateTime, when article created
	* aauthor - article author, instance of User
	* atitle - article title
	* acontent - main body of article textdomain
	* comments - an array for instances of Comment
	***************/
	private $aid, $adate, $aauthor, $atitle, $acontent;
	/* 
	* Note that the Comment class has not been provided, but the comments
	* array is being used to help you see where the comments would be accessed and manipulated
	*/
	private $comments=[];
	
	/**************
	* Creates a 'blank' article, in this implementation articles are
	* instantiated blank, then initialised using initArticle
	* initArticle is called by one of the methods which work with
	* the articleCRUD class. This has been done largely to cope with
	* PHP's inability to handle function overloading for constructors
	**************/
	public function __construct() {
		$this->aid=-1;
		$this->adate=new DateTime();
		$this->aauthor=new User();
		$this->atitle="";
		$this->acontent="";
	}
	
	/**************
	* Setter for Article ID
	**************/
	private function setID($aid){ $this->aid=$aid; }
	
	/**************
	* Setter for datetime, accepts a date in string format
	**************/	
	private function setADate($date) { 
		$this->adate=DateTime::createFromFormat("Y-m-d H:i:s", $date);
		}
		
	/**************
	* Setter for Author, accepts either an instance of User
	* or an integer for the User's ID and creates an instance
	* returns true if instance of User or User is found by ID
	**************/
	private function setAuthor($author) {
		$haveuser=false;		
		if($author instanceof User) {
			$this->aauthor=$author;
			$haveuser=true;
		} else {
			$finduser=new User();
			$haveuser=$finduser->getUserById($author);
			if($haveuser) {
				$this->aauthor=$finduser;
			}
		}
		return $haveuser;
	}
	
	/**************
	* Setter for article title, uses string sanitiser method
	**************/
	private function setTitle($title) {$this->atitle=util::sanStr($title);}
	
	/**************
	* Setter for article content, uses string sanitiser method
	**************/
	private function setContent($content) {$this->acontent=util::sanStr($content);}
	
	/**************
	* Remove all current comments in comments array
	**************/
	public function clearComments() {$this->comments=[];}
	
	/**************
	* Class getters
	**************/
	public function getID() { return $this->aid; }
	public function getADate() { return $this->adate; }
	public function getAuthor() { return $this->aauthor; }
	public function getTitle() { return $this->atitle; }
	public function getContent() { return $this->acontent; }
	public function getCommentList() { return $this->comments;}
	
	/**************
	* Uses commentCRUD to retrieve a recordset of comments as an associative array
	* instantiates Comment for each row and adds to the comments array
	**************/
	public function getCommentsForArticle($aid) {
		$source = new CommentCRUD();
		$data = $source->getCommentsForArticle($aid);
		$comments = [];
	
		if (count($data) > 0) {
			foreach ($data as $row) {
				$comment = new Comment();
				$comment->initComment($row); // Assuming $row contains the data for initComment
				$comments[] = $comment; // Add the processed comment to the list
			}
		}
	
		return $comments; // Return the list of comments
	}
	
	/**************
	* sets article's attributes from a retrieved associative array of an article
	* this will call getCommentsForArticle to initialise the comments
	**************/
	public function initArticle($article) {
		$this->setID($article["blogID"]);
		$this->setADate($article["blogtime"]);
		$this->setAuthor($article["blogposter"]);
		$this->setTitle($article["articletitle"]);
		$this->setContent($article["articletext"]);	
		$this->getCommentsForArticle($this->getID());
	}
	
	/**************
	* Uses ArticleCRUD to retrieve an associative array of an article by articleID
	* if the article is found returns true, otherwise returns false
	* calls initArticle should the article be found
	**************/
	public function getArticleById($aid) {
		$havearticle=false;
		$source=new ArticleCRUD();
		$data=$source->getArticleById($aid);
		if(count($data)==1) {
			$article=$data[0];
			$this->initArticle($article);
			$havearticle=true;
		}
		return $havearticle;
	}
	
	/**************
	* Calls ArticleCRUD 
	* accepts single input paramater of a date
	* Calls initArticle and returns true if article found
	* otherwise returns false
	**************/
	public function getNextArticle($start) {
		$havearticle=false;
		$source=new ArticleCRUD();
		$data=$source->getArticles($start,1,'NEXT');
		if(count($data)==1) {
			$article=$data[0];
			$this->initArticle($article);
			$havearticle=true;
		}
		return $havearticle;		
	}

	/**************
	* Calls ArticleCrud 
	* accepts single input paramater of a date
	* Calls initArticle and returns true if article found
	* otherwise returns false
	**************/	
	public function getPrevArticle($start) {
		$havearticle=false;
		$source=new ArticleCRUD();
		$data=$source->getArticles($start,1,'PREV');
		if(count($data)==1) {
			$article=$data[0];
			$this->initArticle($article);
			$havearticle=true;
		}
		return $havearticle;		
	}

	/**************
	* Gets the last article created based on date using
	* ArticleCRUD getLastArticle. Uses initArticle and returns true
	* if an article is found, otherwise returns false
	**************/	
	public function getLastArticle() {
		$havearticle=false;
		$source=new ArticleCRUD();
		$data=$source->getLastArticle();
		if(count($data)==1) {
			$article=$data[0];
			$this->initArticle($article);
			$havearticle=true;
		}
		return $havearticle;		
	}

	/**************
	* Accepts a User ID as an input parameter
	* returns the last article created by the user
	* calls initArticle and returns true if an article is found
	* otherwise returns false
	**************/
	public function getLastUserArticle($poster) {
		$havearticle=false;
		$source=new ArticleCRUD();
		$data=$source->getLastUserArticle($poster);
		if(count($data)==1) {
			$article=$data[0];
			$this->initArticle($article);
			$havearticle=true;
		}
		return $havearticle;		
	}

		/**************
	* Accepts 3 input parameters to create a new Article
	* Author is set using setAuthor, accepts an id or instance of author
	* Calls ArticleCrud addArticle and getLastUserArticle to add new article
	* if successful article ID and time set from database
	* returns article ID if successful, 0/false if unsuccessful with an
	* error message as an associative array
	**************/	
	public function addArticle($author,$title,$content) {
		$aid=0;
		$insert=0;
		$messages="";
		if($this->setAuthor($author)) {
			$target=new ArticleCRUD();
			$this->setTitle($title);
			$this->setContent($content);
			$insert=$target->addArticle($this->getTitle(),$this->getContent(),$this->getAuthor()->getUserid());
			if($insert!=1) { $messages.=$insert;$insert=0; }
			else {
				$resultset=$target->getLastUserArticle($this->getAuthor()->getUserid());
				if(count($resultset)==1) {
					$this->setID($resultset[0]["blogID"]);
					$this->setADate($resultset[0]["blogtime"]);
					$insert=$this->getID();
				}
				
			}
		} else { $messages="Invalid Poster<br>"; }
		$result=['insert'=>$insert,'messages'=>$messages];
		return $result;
		
	}

	
	
	/**************
	* If the current article is value (aid!=-1) this will
	* update the associate articles title and content. Uses
	* ArticleCRUD updateArticle method.
	* if successful will return an associative array indicating successful
	* else will return an error message
	**************/	
	public function updateArticle($title,$content,$aid) {
		$messages="";
		$update=0;
		$found=$this->getArticleById($aid);
		$target=new ArticleCRUD();
		if($found) {
			if(util::posted($title)){$messages.=$this->setTitle($title);}
			if(util::posted($content)){$messages.=$this->setContent($content);}
			if($messages=="") {
				$update=$target->updateArticle($this->getTitle(), $this->getContent(), $aid);
				if($update!=1) {$messages=$update;$update=0;}
			}			
		}
		$result=['update'=>$update,'messages'=>$messages];
		return $result;
	}
	
	/**************
	* If the current article is value (aid!=-1) this will
	* delete the associate article. Uses
	* ArticleCRUD deleteArticle method.
	* if successful will return an associative array indicating successful
	* else will return an error message
	**************/		
	public function deleteArticle($aid) {
		$messages="";
		$delete=0;
		$found=$this->getArticleById($aid);
		$target=new ArticleCRUD();
		if($found) {
			if($messages=="") {
				$delete=$target->deleteArticle($aid);
				if($delete!=1) {$messages=$delete;$delete=0;}
			}			
		}
		$result=['delete'=>$delete,'messages'=>$messages];
		return $result;		
	}

	/**************
	* Converts the Article object and any Comment objects in the comments
	* array into an associative array representation
	* Uses the Comment class toArray method
	* Any object elements, like author or date, are converted to string
	* representations
	**************/		
	public function toArray() {
		$comments=[];
		
		foreach($this->comments as $comment) {
			array_push($comments, $comment->toArray());
		}
		
		$outarray=array(
			'id' => htmlentities($this->getID()),
			'title' => htmlentities($this->getTitle()),
			'content' => htmlentities($this->getContent()),
			'author' => htmlentities($this->getAuthor()->getUsername()),
			'date' => htmlentities($this->getADate()->format("Y-m-d H:i:s")),
			'comments' => $comments
		);
		return $outarray;
	}
	/**************
	* toString method converts the article and associated 
	* comments to HTML output
	**************/		
	public function __toString() {
		$output="<article id='a".htmlentities($this->getID())."'>";
		$output.="<h1>".htmlentities($this->getTitle())."</h1>";
		$output.="<p>".nl2br(htmlentities($this->getContent()))."</p>";
		$output.="<footer>Written by ".htmlentities($this->getAuthor()->getUsername())." on ".htmlentities($this->getADate()->format("Y-m-d"))." at ".htmlentities($this->getADate()->format("H:i:s"))."</footer>";
		foreach($this->comments as $comment) {
			$output.=$comment;						
		}
		$output.="</article>";
		return $output;
	}
	
}

?>
