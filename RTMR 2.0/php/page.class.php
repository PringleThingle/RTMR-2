<?php
require_once("user.class.php");
require_once("menu.class.php");
require_once("movie.class.php");
require_once("review.class.php");

class Page {
	private $user, $pagetype, $isauthenticated, $menu;
	private $movies=[];
	private $reviews=[];

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
		$this->menu = new Menu($this->getUser()->getUserLevel());
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
		if(isset($_SESSION['userID']) && $_SESSION['userID']!="") {
			$this->setStatus($this->getUser()->authIdSession($_SESSION['userID'],session_id()));
		}
		if((!$this->getStatus() && $this->getPagetype()>0) || ($this->getStatus() && $this->getUser()->getUserLevel()<$this->getPagetype())) {
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
			$_SESSION['userID']=$this->getUser()->getUserid();

			switch($this->getUser()->getUserLevel()) {
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
		if(isset($_SESSION['userID']) && $_SESSION['userID']!="") {
			$this->getUser()->storeSession($_SESSION['userID']);
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
	public function updateUser($username,$password,$email,$userid, $usertype) {
		if($this->getUser()->getUserLevel()==3 || $this->getUser()->getUserid()==$userid) {
			$usertoupdate=new User();
			$usertoupdate->getUserById($userid);
			if($this->getUser()->getUserLevel()!=3) {
				$usertype="";
			}
			$result=$usertoupdate->updateUser($username,$password,$email,$usertype, $userid);
			return $result;
			
		}
	}	

	/************
	* Empty movie array
	************/
	public function clearMovies() { $this->movies=[];}

	/************
	* Return all movies in movie array
	************/
	public function getMovieList() { return $this->movies;}
	
	/************
	* Uses MovieCRUD class to retrieve an associative array of movies
	* associative array Uses Movie class to create an array of instances
	* of Movie, stored in the movie array
	* accepts 3 parameters
	* @param start start date of query as a string
	* @param qty maximum number of movies to return
	* @param direction default DESC, order to retrieve movies
	* @return havemovies 0 if no movies found, >0 if movies found
	************/	
	public function getMovies($start,$qty,$direction='DESC') {
		$havemovies=0;
		$source=new MovieCRUD();
		$movies=$source->getMovies($start,$qty,$direction);
		if(count($movies)>0) {
			$havemovies=count($movies);
			foreach($movies as $movie) {
				$newmovie=new Movie();
				$newmovie->initMovie($movie);
				array_push($this->movies, $newmovie);
			}
		}
		return $havemovies;
	}

	/*************
	* returns a movie from a movie id ($mid)
	**************/

	public function getMovie($mid) {
		$source=new Movie();
		if($movie=$source->getMovieByID($mid)) {
			array_push($this->movies,$movie);
			return true;
		} else {return false;}
	}
	
	/*************
	* creates and returns a string from the movies stored
	* creates menus based upon the currently logged in user
	**************/

	public function displayMovies() {
		$output = "";

		foreach ($this->movies as $movie) {
			if (!$movie || !is_object($movie)) {
				continue;
			}
		
			$output .= "<h2 class='movietitle'>" . htmlentities($movie->getTitle()) . "</h2>";
			$output .= "<div class='movie-item' data-watched-date='" . htmlentities($movie->getWDate()->format("Y-m-d H:i:s")) . "' data-movie-id='" . htmlentities($movie->getID()) . "'>";

			$output .= "<div class='movie-content'>";
			$output .= "<img class='movie-poster' src='" . htmlentities($movie->getPosterLink()) . "' alt='" . htmlentities($movie->getTitle()) . " Poster'>";
			// Menu for edit/delete/add review
			$output .= "<ul class='moviemenu'>";
			if ($this->getStatus() && $this->getUser()->getUserLevel() >= 0) {
				
				if ($this->getUser()->getUserLevel() >= 3) {
					$output .= "<li><button class='moviebutton'><a class='moviebuttontext' href='deletemovie.php?mid=" . $movie->getID() . "' onclick='return confirm(\"Are you sure you want to delete this movie?\");'>Delete</a></button></li>";
				}
				if ($this->getStatus() && $this->getUser()->getUserLevel() >= 2) {
				$output .= "<li><button class='moviebutton'><a class='moviebuttontext'href='addreview.php?mid=" . $movie->getID() . "'>Add Review</a></button></li>";
				}
				
			}
			$output .= "<li><p class='homerating'>{$movie->getCombinedRating($movie->getID())}⭐</p></li>";
			$output .= "</ul>";
			$output .= "</div>";  // Close movie-content
		
			// Display reviews
			$output .= "<section class='reviews'>";
			$output .= "<h2 class='reviewstitle'>Reviews</h2>";
		
			$reviews = $movie->getReviewsForMovie($movie->getID());
			if (!empty($reviews)) {
				foreach ($reviews as $review) {
					$output .= "<div class='review'>";
					$output .= "<p class='reviewtext'>" . nl2br(htmlentities($review->getText())) . "</p>";
					$output .= "<ul class='userreviewinfo'>";
					$output .= "<li class='userreviewli'><p class='userrating'>{$review->getRating()}⭐  </p></li>";
					$output .= "<li class='userreviewli'><strong class = 'reviewer'>By " . htmlentities($review->getAuthor()->getUsername()) . " on " . htmlentities($review->getRDate()->format("d-m-Y")) . "</strong></li>";
					$output .= "</ul>";
				$menuItems = "";

				// Check for "Edit" 
				if (($this->getStatus() && $this->getUser()->getUserLevel() >= 3) || ($this->getStatus() && $this->getUser()->getUserid() == $review->getAuthor()->getUserid())) {
					$menuItems .= "<li><button class='moviebutton'><a class='moviebuttontext' href='editreview.php?rid=" . $review->getRID() . "'>Edit</a></button></li>";
				}
				
				// Check for "Delete" 
				if (($this->getStatus() && $this->getUser()->getUserLevel() >= 3) || ($this->getStatus() && $this->getUser()->getUserid() == $review->getAuthor()->getUserid())) {
					$menuItems .= "<li><button class='moviebutton'><a class='moviebuttontext' href='deletereview.php?rid=" . $review->getRID() . "' onclick='return confirm(\"Do you want to delete this review?\");'>Delete</a></button></li>";
				}
				
				if (!empty($menuItems)) {
					$output .= "<ul class='reviewmenu'>" . $menuItems . "</ul>";
				}

				$output .= "</div>"; // Close review

			}
			
			} else {
				$output .= "<p class='noreviewtext'>No reviews yet. Be the first to review!</p>";
			}
			
			$output .= "</section>";  // Close reviews section
			$output .= "</div>";  // Close movie-item
		}
		
		return $output;
	}
	
	
	/*************
	* creates and returns an array of information for each movie including their reviews
	**************/
	public function moviesToArray() {
		$output=[];
		$edit=false;
		$delete=false;
		$add=false;
		foreach($this->movies as $movie) {
			$reviewsarray=[];
			$reviews = $movie->getReviewsForMovie($movie->getID());
			foreach($reviews as $review) {
				$redit=false;
				$rdelete=false;
				$reviewarray=$review->toArray();

				if($this->getStatus() && ($this->getUser()->getUserLevel()>=3 || ($this->getUser()->getUserLevel()>=2 && $this->getUser()->getUserid()== $review->getAuthor()->getUserid()))) {
					$redit=true;
					$rdelete=true;	
				}

				$reviewarray['edit']=$redit;
				$reviewarray['delete']=$rdelete;
				array_push($reviewsarray,$reviewarray);
			}
			if($this->getStatus() && $this->getUser()->getUserLevel()>=3) {
				$edit=true;
				$delete=true;
			}
			if($this->getStatus() && $this->getUser()->getUserLevel()>=2) {
				$add=true;
			
			}
			$moviearray=$movie->toArray();
			$moviearray['add'] = $add;
			$moviearray['edit'] = $edit;
			$moviearray['delete'] = $delete;
			$moviearray['comments'] = $reviewsarray;
			array_push($output,$moviearray);			
		}
		return $output;
	}
}
?>