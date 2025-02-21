<?php

require_once("moviecrud.class.php");
require_once("user.class.php");
require_once("directorcrud.class.php");
require_once("util.class.php");

/**************
* Movie class
**************/
class Movie {
	/***************
	* mid - serial article id
	* mdate - instance of DateTime, when movie was actually made
	* wdate - date that the movie was added by a user
	* mdirector - director of a movie
	* mtitle - movie title
	* mdescription - description of the movie from TMDB (Not currently used but may be in future)
	* mposterlink - movie poster link from TMDB (used to display posters)
	***************/
	private $mid, $mdate, $wdate, $mdirector, $mtitle, $mdescription, $mposterlink;

	private $reviews=[];
	
	/**************
	* Creates a 'blank' movie, in this implementation movies are
	* instantiated blank, then initialised using initMovie
	* initMovie is called by one of the methods which work with
	* the movieCRUD class. This has been done largely to cope with
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
	* Setter for Movie Date, accepts a date in string format
	**************/	
	private function setMDate($date) { 
		$this->mdate=DateTime::createFromFormat("Y-m-d", $date);
		}

	/**************
	* Setter for Watched Date, accepts a date in string format
	**************/	
	private function setWDate($date) {
		$this->wdate = DateTime::createFromFormat("Y-m-d H:i:s", $date);
		if ($this->wdate === false) {
			error_log("Invalid watchedDate: $date");
			$this->wdate = new DateTime("1970-01-01 00:00:00");  // Default fallback
		}
	}
	
	/**************
	* Setter for movie title, uses string sanitiser method
	**************/
	private function setTitle($title) {$this->mtitle=util::sanStr($title);}
	
	/**************
	* Setter for movie description, uses string sanitiser method
	**************/
	private function setDescription($content) {$this->mdescription=util::sanStr($content);}
	
	/**************
	* Remove all current reviews in reviews array
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
	* Uses reviewCRUD to retrieve a recordset of reviews as an associative array
	* instantiates Review for each row and adds to the reviews array
	**************/
	public function getReviewsForMovie($mid) {
		$source = new ReviewCRUD();
		$data = $source->getReviewsForMovie($mid);
		$reviews = [];
	
		if (count($data) > 0) {
			foreach ($data as $row) {
				$review = new Review();
				$review->initReview($row); 
				$reviews[] = $review; 
			}
		}
	
		return $reviews; // Return the list of reviews
	}
	
	/**************
	* sets movie's attributes from a retrieved associative array of a movie
	* this will call getReviewsForMovie to initialise the reviews
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
	* Uses MovieCRUD to retrieve an associative array of a movie by movieID
	* if the movie is found returns true, otherwise returns false
	* calls initMovie should the movie be found
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

	/**************
	* Returns a directors name from a movieID
	**************/
	public function getDirectorNameByMovieID($mid) {
		$source = new DirectorCRUD();
		$directorName = $source->getDirectorNameByMovieID($mid);
	
		if ($directorName !== "Director not found.") {
			return $directorName;  // Return the director's name
		}
	
		return "Unknown Director";  // Return a placeholder if not found
	}
	
	
	
	/**************
	* Calls MovieCRUD 
	* accepts single input paramater of a date
	* Calls initMovie and returns true if a movie is found
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
	* Calls MovieCRUD 
	* takes in a movieID and returns an average of all the ratings for that movie
	* or returns 0 if no ratings are found
	**************/
	public function getCombinedRating($mid) {
		$source = new MovieCRUD();
		$data = $source->getCombinedRating($mid);
	
		if (!empty($data) && isset($data[0]['averageRating'])) {
			return round($data[0]['averageRating'], 1); // Return average rating
		}
	
		return "0"; // Default if no ratings exist
	}

	/**************
	* Calls MovieCrud 
	* accepts single input paramater of a date
	* Calls initMovie and returns true if movie is found
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
	* Accepts 6 input parameters to create a new Movie
	* Calls MovieCrud addMovie to add the new movie
	* if successful the watched date is set automatically in the database
	* returns an error message if add fails
	**************/	

	public function addMovie($mid, $title, $description, $releaseDate, $posterLink, $director) {
		$insert = 0;
		$messages = "";
	
		if ($this->setID($mid)) {
			$target = new MovieCRUD();
			$this->setID($mid); 
			$this->setTitle($title);
			$this->setDescription($description);
			$this->setMDate($releaseDate);
			$this->setPosterLink($posterLink);
			$this->setDirectorID($director);
	
			$insert = $target->addMovie($this->getID(), $this->getTitle(), $this->getDescription(), $this->getMDate(), $this->getPosterLink(), $this->getDirectorID());
	
			if ($insert !== 1) {
				$messages .= $insert;
				$insert = 0;
			}
		} else {
			die("setID() failed for mid: " . htmlspecialchars($mid));
		}
	
		return ['insert' => $insert, 'messages' => $messages];
	}
	
	/**************
	* If the current article is value (mid!=-1) this will
	* delete the associated movie. 
	* Uses MovieCRUD deleteMovie method.
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
	* Converts the Movie object and any Review objects in the reviews
	* array into an associative array representation
	* Uses the Review class toArray method
	* Any object elements like date are converted to string
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
	* toString method converts the movie and associated 
	* reviews to HTML output
	**************/		
	public function __toString() {
		$output="<movie id='a".htmlentities($this->getID())."'>";
		$output.="<h1>".htmlentities($this->getTitle())."</h1>";
		$output.="<p>".nl2br(htmlentities($this->getDescription()))."</p>";
		$output.="<footer>Watched on".htmlentities($this->getMDate()->format("Y-m-d")." at ".htmlentities($this->getMDate()->format("H:i:s")))."</footer>";
		foreach($this->reviews as $review) {
			$output.=$review;						
		}
		$output.="</movie>";
		return $output;
	}
	
}

?>
