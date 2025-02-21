<?php
require_once("db.php");

/*****************
* Movie CRUD class
*****************/
class MovieCRUD {
	/*************
	* Standard constructor and database connection features
	*************/
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	
	/**************
	* Returns an associative array where movieID = mid
	* will return an empty array if no movie found
	**************/	
	public function getMovieById($mid, $style=MYSQLI_ASSOC) {
		$this->sql="select * from movieInfo where movieID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$mid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;		
	}

	/**************
	* Returns an average of all user ratings for a movie
	* will return an empty array if no movie found
	**************/	
		public function getCombinedRating($movieID, $style=MYSQLI_ASSOC) {
		$this->sql="select AVG(userRating) AS averageRating FROM movieReviews WHERE movieID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$movieID);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Returns an associative array for the last movie added
	* will return an empty array if no movie found
	**************/		
	public function getLastMovies($style=MYSQLI_ASSOC) {
		$this->sql="select * from movieInfo order by movieDate desc limit 1";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
	
	/**************
	* A general purpose method for getting multiple movies,
	* Returns an associative array of movies
	* @param start A starting date for the query
	* @param qty The maximum number of movies to return
	* @param direction optional default DESC - compared against a whitelist to prevent injection
	* @param style optional default MYSQLI_ASSOC - type of array to return for query
	**************/	
	public function getMovies($start,$qty,$direction='DESC',$style=MYSQLI_ASSOC) {
		switch($direction) {
			case "ASC":
				$comparator='<=';break;
			case "PREV":
				$comparator='<';$direction='DESC';break;
			case "NEXT":
				$comparator='>';$direction='ASC';break;
			default:
				$comparator='<=';$direction='DESC';break;
		}
		$this->sql="select * from movieInfo where watchedDate $comparator ? order by watchedDate $direction limit ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("si",$start,$qty);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;			
	}

	/**************
	* adds a new Movie, returns 1 on success, error message on fail
	**************/		

	public function addMovie($mid, $title, $description, $releasedate, $posterLink, $directorID) {
		// Validate that directorID exists
		$this->sql = "SELECT directorID FROM directorInfo WHERE directorID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i", $directorID);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
	
		if ($result->num_rows === 0) {
			return "Error: The provided directorID ($directorID) does not exist in directorInfo.<br />";
		}
	
		if ($releasedate instanceof DateTime) {
			$releasedate = $releasedate->format('Y-m-d');
		}
	
		// Prepare the SQL for inserting the movie
		$this->sql = "INSERT INTO movieInfo (movieID, title, movieDescription, releaseDate, posterLink, directorID) 
					  VALUES (?, ?, ?, ?, ?, ?)";
		$this->stmt = self::$db->prepare($this->sql);
	
		if (!$this->stmt) {
			return "SQL Preparation Error: " . self::$db->error . "<br />";
		}
	
		$this->stmt->bind_param("issssi", $mid, $title, $description, $releasedate, $posterLink, $directorID);
	
		if (!$this->stmt->execute()) {
			return "SQL Execution Error: " . $this->stmt->error . "<br />" .
				   "SQL Query: " . $this->sql . "<br />" .
				   "Parameters: [movieID: $mid, title: $title, description: $description, releaseDate: $releasedate, posterLink: $posterLink, directorID: $directorID]<br />";
		}
	
		if ($this->stmt->affected_rows !== 1) {
			return "Could not add movie. No rows affected.<br />" .
				   "SQL Query: " . $this->sql . "<br />" .
				   "Parameters: [movieID: $mid, title: $title, description: $description, releaseDate: $releasedate, posterLink: $posterLink, directorID: $directorID]<br />";
		}
	
		return 1;  // Return 1 on success
	}

	/**************
	* Deletes existing movie, returns 1 on success, error message on fail
	**************/		
	public function deleteMovie($mid) {
		$this->sql = "DELETE FROM movieInfo WHERE movieID=?;";
		$this->stmt = self::$db->prepare($this->sql);

		if (!$this->stmt) {
			return ['update' => 0, 'messages' => self::$db->error];
		}
	
		$this->stmt->bind_param("i", $mid);
		$result = $this->stmt->execute();
	
		if ($result) {
			return ['update' => 1, 'messages' => "Delete successful"];
		} else {
			return ['update' => 0, 'messages' => $this->stmt->error];
		}

	}

}

?>



