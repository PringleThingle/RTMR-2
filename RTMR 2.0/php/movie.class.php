<?php

require_once("moviecrud.class.php");
require_once("user.class.php");
require_once("directorcrud.class.php");
//require_once("comment.class.php");
require_once("util.class.php");

/**************
* Article class
**************/
class Movie {
	/***************
	* mid - serial article id
	* mdate - instance of DateTime, when article created
	* mrating - overall review based on users reviews
	* mdirector - director of a movie
	* atitle - article title
	* acontent - main body of article textdomain
	* comments - an array for instances of Comment
	***************/
	private $mid, $mdate, $wdate, $mrating, $mdirector, $mtitle, $mdescription, $mposterlink;
	/* 
	* Note that the Comment class has not been provided, but the comments
	* array is being used to help you see where the comments would be accessed and manipulated
	*/
	private $reviews=[];
	
	/**************
	* Creates a 'blank' article, in this implementation articles are
	* instantiated blank, then initialised using initArticle
	* initArticle is called by one of the methods which work with
	* the articleCRUD class. This has been done largely to cope with
	* PHP's inability to handle function overloading for constructors
	**************/
	public function __construct() {
		$this->mid=-1;
		$this->mdate=new DateTime();
		$this->wdate=new DateTime();
		$this->mdirector="";
		$this->mtitle="";
		$this->mdescription="";
		$this->mposterlink="";
	}
	
	/**************
	* Setter for Movie ID
	**************/
	private function setID($mid) {
		$this->mid = $mid;
		return true; 
	}

	/**************
	* Setter for Director ID 
	**************/
	private function setDirectorID($mdirector){ $this->mdirector=$mdirector; }

	/**************
	* Setter for Poster Link
	**************/
	private function setPosterLink($mposterlink){ $this->mposterlink=$mposterlink; }
	
	/**************
	* Setter for datetime, accepts a date in string format
	**************/	
	private function setMDate($date) { 
		$this->mdate=DateTime::createFromFormat("Y-m-d", $date);
		}

	private function setWDate($date) {
		$this->wdate = DateTime::createFromFormat("Y-m-d H:i:s", $date);
		if ($this->wdate === false) {
			error_log("Invalid watchedDate: $date");
			$this->wdate = new DateTime("1970-01-01 00:00:00");  // Default fallback
		}
	}
	
	/**************
	* Setter for article title, uses string sanitiser method
	**************/
	private function setTitle($title) {$this->mtitle=util::sanStr($title);}
	
	/**************
	* Setter for article content, uses string sanitiser method
	**************/
	private function setDescription($content) {$this->mdescription=util::sanStr($content);}
	
	/**************
	* Remove all current comments in comments array
	**************/
	public function clearReviews() {$this->reviews=[];}
	
	/**************
	* Class getters
	**************/
	public function getID() { return $this->mid; }
	public function getMDate() { return $this->mdate; }
	public function getWDate() { return $this->wdate; }
	public function getTitle() { return $this->mtitle; }
	public function getDescription() { return $this->mdescription; }
	public function getReviewList() { return $this->reviews;}
	public function getPosterLink() { return $this->mposterlink;}
	public function getDirectorID() { return $this->mdirector;}
	
	/**************
	* Uses commentCRUD to retrieve a recordset of comments as an associative array
	* instantiates Comment for each row and adds to the comments array
	**************/
	public function getReviewsForMovie($mid) {
		$source = new ReviewCRUD();
		$data = $source->getReviewsForMovie($mid);
		$reviews = [];
	
		if (count($data) > 0) {
			foreach ($data as $row) {
				$review = new Review();
				$review->initReview($row); // Assuming $row contains the data for initComment
				$reviews[] = $review; // Add the processed comment to the list
			}
		}
	
		return $reviews; // Return the list of comments
	}
	
	/**************
	* sets article's attributes from a retrieved associative array of an article
	* this will call getCommentsForArticle to initialise the comments
	**************/
	public function initMovie($movie) {
		$this->setID($movie["movieID"]);
		$this->setMDate($movie["releaseDate"]);
		$this->setWDate($movie["watchedDate"]);
		$this->setTitle($movie["title"]);
		$this->setDirectorID($movie["directorID"]);
		$this->setPosterLink($movie["posterLink"]);
		$this->setDescription($movie["movieDescription"]);	
		$this->getReviewsForMovie($this->getID());
	}
	
	/**************
	* Uses ArticleCRUD to retrieve an associative array of an article by articleID
	* if the article is found returns true, otherwise returns false
	* calls initArticle should the article be found
	**************/
	public function getMovieById($mid) {
		$havemovie=false;
		$source=new MovieCRUD();
		$data=$source->getMovieById($mid);
		if(count($data)==1) {
			$movie=$data[0];
			$this->initMovie($movie);
			$havemovie=true;
		}
		return $havemovie;
	}

		/**************
	* Uses ArticleCRUD to retrieve an associative array of an article by articleID
	* if the article is found returns true, otherwise returns false
	* calls initArticle should the article be found
	**************/

	public function getDirectorByID($id) {
		$havedirector=false;
		$source=new DirectorCRUD();
		$data=$source->getDirectorByID($id);
		if(count($data)==1) {
			$director=$data[0];
			$havedirector=true;
		} 
		return $havedirector;
	}

	public function getDirectorNameByMovieID($mid) {
		$source = new DirectorCRUD();
		$directorName = $source->getDirectorNameByMovieID($mid);
	
		if ($directorName !== "Director not found.") {
			return $directorName;  // Return the director's name
		}
	
		return "Unknown Director";  // Return a placeholder if not found
	}
	
	
	
	/**************
	* Calls ArticleCRUD 
	* accepts single input paramater of a date
	* Calls initArticle and returns true if article found
	* otherwise returns false
	**************/
	public function getNextMovie($start) {
		$havemovie=false;
		$source=new MovieCRUD();
		$data=$source->getMovies($start,1,'NEXT');
		if(count($data)==1) {
			$movie=$data[0];
			$this->initMovie($movie);
			$havemovie=true;
		}
		return $havemovie;		
	}

	/**************
	* Calls ArticleCrud 
	* accepts single input paramater of a date
	* Calls initArticle and returns true if article found
	* otherwise returns false
	**************/	
	public function getPrevMovie($start) {
		$havemovie=false;
		$source=new MovieCRUD();
		$data=$source->getMovies($start,1,'PREV');
		if(count($data)==1) {
			$movie=$data[0];
			$this->initMovie($movie);
			$havemovie=true;
		}
		return $havemovie;		
	}

	/**************
	* Accepts 3 input parameters to create a new Article
	* Author is set using setAuthor, accepts an id or instance of author
	* Calls ArticleCrud addArticle and getLastUserArticle to add new article
	* if successful article ID and time set from database
	* returns article ID if successful, 0/false if unsuccessful with an
	* error message as an associative array
	**************/	

	public function addMovie($mid, $title, $description, $releaseDate, $posterLink, $director) {
		$insert = 0;
		$messages = "";
	
		if ($this->setID($mid)) {
			echo($releaseDate);
			$target = new MovieCRUD();
			$this->setID($mid); 
			$this->setTitle($title);
			$this->setDescription($description);
			$this->setMDate($releaseDate);
			$this->setPosterLink($posterLink);
			$this->setDirectorID($director);
	
			$insert = $target->addMovie($this->getID(), $this->getTitle(), $this->getDescription(), $this->getMDate(), $this->getPosterLink(), $this->getDirectorID());
	
			if ($insert !== 1) {
				$messages .= $insert;  // Collect the error message
				$insert = 0;
			}
		} else {
			die("setID() failed for mid: " . htmlspecialchars($mid));
		}
	
		return ['insert' => $insert, 'messages' => $messages];
	}
	

	/**************
	* If the current article is value (aid!=-1) this will
	* update the associate articles title and content. Uses
	* ArticleCRUD updateArticle method.
	* if successful will return an associative array indicating successful
	* else will return an error message
	**************/	
	// public function updateMovie($title,$content,$aid) {
	// 	$messages="";
	// 	$update=0;
	// 	$found=$this->getArticleById($aid);
	// 	$target=new ArticleCRUD();
	// 	if($found) {
	// 		if(util::posted($title)){$messages.=$this->setTitle($title);}
	// 		if(util::posted($content)){$messages.=$this->setContent($content);}
	// 		if($messages=="") {
	// 			$update=$target->updateArticle($this->getTitle(), $this->getContent(), $aid);
	// 			if($update!=1) {$messages=$update;$update=0;}
	// 		}			
	// 	}
	// 	$result=['update'=>$update,'messages'=>$messages];
	// 	return $result;
	// }
	
	/**************
	* If the current article is value (aid!=-1) this will
	* delete the associate article. Uses
	* ArticleCRUD deleteArticle method.
	* if successful will return an associative array indicating successful
	* else will return an error message
	**************/		
	public function deleteMovie($mid) {
		$messages="";
		$delete=0;
		$found=$this->getMovieById($mid);
		$target=new MovieCRUD();
		if($found) {
			if($messages=="") {
				$delete=$target->deleteMovie($mid);
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
		$reviews=[];
		
		foreach($this->reviews as $review) {
			array_push($reviews, $review->toArray());
		}
		
		$outarray=array(
			'mid' => htmlentities($this->getID()),
			'title' => htmlentities($this->getTitle()),
			'description' => htmlentities($this->getDescription()),
			'director' => htmlentities($this->getDirectorID()),
			'posterlink' => htmlentities($this->getPosterLink()),
			'date' => htmlentities($this->getMDate()->format("Y-m-d H:i:s")),
			'reviews' => $reviews
		);
		return $outarray;
	}
	/**************
	* toString method converts the article and associated 
	* comments to HTML output
	**************/		
	public function __toString() {
		$output="<movie id='a".htmlentities($this->getID())."'>";
		$output.="<h1>".htmlentities($this->getTitle())."</h1>";
		$output.="<p>".nl2br(htmlentities($this->getDescription()))."</p>";
		$output.="<footer>Watched on".htmlentities($this->getMDate()->format("Y-m-d")." at ".htmlentities($this->getMDate()->format("H:i:s")))."</footer>";
		foreach($this->reviews as $review) {
			$output.=$review;						
		}
		$output.="</article>";
		return $output;
	}
	
}

?>
