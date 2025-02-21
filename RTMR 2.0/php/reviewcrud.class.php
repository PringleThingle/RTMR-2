<?php
require_once("db.php");
class ReviewCRUD {
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	

	/**************
	* Returns an array of reviews associated with a specific movie ID ($mid)
	**************/	
	public function getReviewsForMovie($mid, $style=MYSQLI_ASSOC) {
		$this->sql="select * from movieReviews where movieID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$mid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Returns a review from a specific review ID ($rid)
	**************/	
	public function getReviewById($rid, $style = MYSQLI_ASSOC) {
		$rid = (int)$rid;

		$this->sql = "SELECT * FROM movieReviews WHERE reviewID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		
		$this->stmt->bind_param("i", $rid);
	
		$this->stmt->execute();
	
		$result = $this->stmt->get_result();
	
		$resultset = $result->fetch_array($style);

		return $resultset ?: false;
	}

	/**************
	* adds a review into the database, returns 1 on success, 0 on failure
	**************/	
    public function addReview($text, $poster, $movieID, $rating) {
        $this->sql = "INSERT INTO movieReviews (reviewText, reviewPoster, movieID, userRating) VALUES (?, ?, ?, ?)";
        $this->stmt = self::$db->prepare($this->sql);
    
        if (!$this->stmt) {
            return 0; 
        }
    
        $this->stmt->bind_param("siid", $text, $poster, $movieID, $rating);
    
        if (!$this->stmt->execute()) {
            return 0; 
        }
    
        return 1; 
    }

	/**************
	* Returns the last review a specific user made
	**************/	

	public function getLastUserReview($poster, $style=MYSQLI_ASSOC) {
		$this->sql="select * from movieReviews where reviewPoster=? order by reviewTime desc limit 1";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$poster);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Returns the last review that was made
	**************/	
    public function getLastReview($style=MYSQLI_ASSOC) {
		$this->sql="select * from movieReviews order by reviewTime desc limit 1";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Updates a review in the database when a user edits 
	**************/	
	public function updateReview($text, $rid, $rating) {
		$this->sql = "UPDATE movieReviews SET reviewText=?, userRating=? WHERE reviewID=?;";
		$this->stmt = self::$db->prepare($this->sql);
	
		if (!$this->stmt) {
			return ['update' => 0, 'messages' => "Prepare failed: " . self::$db->error];
		}
	
		$this->stmt->bind_param("sdi", $text, $rating, $rid);
		$result = $this->stmt->execute();
	
		if ($result) {
			return ['update' => 1, 'messages' => "Update successful"];
		} else {
			return ['update' => 0, 'messages' => "Execution failed: " . $this->stmt->error];
		}
	}

	/**************
	* Deletes existing review, returns 1 on success, error message on fail
	**************/		
	public function deleteReview($rid) {
		$this->sql = "DELETE FROM movieReviews WHERE reviewID = ?;";
		$this->stmt = self::$db->prepare($this->sql);
	
		if (!$this->stmt) {
			return ['delete' => 0, 'messages' => "Prepare failed: " . self::$db->error];
		}
	
		$this->stmt->bind_param("i", $rid);
		$result = $this->stmt->execute();
	
		if ($result) {
			return ['delete' => 1, 'messages' => "Delete successful"];
		} else {
			return ['delete' => 0, 'messages' => "Execution failed: " . $this->stmt->error];
		}
	}
    
}

?>