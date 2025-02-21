<?php

require_once("moviecrud.class.php");
require_once("user.class.php");
require_once("reviewcrud.class.php");
require_once("util.class.php");

/**************
* Review class
**************/
class Review {
	/***************
	* mid - serial movie id
	* rid - serial review id
	* rating - user rating for a movie
	* rdate - instance of DateTime, when review was created
	* rauthor - review author, instance of User
	* rtext - main body of review textdomain
	* reviews - an array for instances of Review
	***************/
	private $mid, $rid, $rating, $rdate, $rauthor,$rtext;
	private $reviews=[];
	
	/**************
	* Creates a 'blank' review, in this implementation reviews are
	* instantiated blank, then initialised using initReview
	* initReview is called by one of the methods which work with
	* the reviewCRUD class. This has been done largely to cope with
	* PHP's inability to handle function overloading for constructors
	**************/
	public function __construct() {
		$this->mid=-1;
        $this->rid=-1;
		$this->rdate=new DateTime();
		$this->rauthor=new User();
		$this->rtext="";
		$this->rating=0.0;
	}
	
	/**************
	* Setters for Review & Movie ID
	**************/
    private function setRID($rid){ $this->rid=$rid; }
	private function setMID($mid){ $this->mid=$mid; }

	/**************
	* Setter for Review rating
	**************/
	private function setRating($rating) {$this->rating=util::sanFloat($rating);}
	
	/**************
	* Setter for datetime, accepts a date in string format
	**************/	
	private function setRDate($date) { 
		$this->rdate=DateTime::createFromFormat("Y-m-d H:i:s", $date);
	}
		
	/**************
	* Setter for Author, accepts either an instance of User
	* or an integer for the User's ID and creates an instance
	* returns true if instance of User or User is found by ID
	**************/

	private function setAuthor($author) {
		$haveuser = false;
		if ($author instanceof User) {
			$this->rauthor = $author;
			$haveuser = true;
		} else {
			$finduser = new User();
			$haveuser = $finduser->getUserById($author);
			if ($haveuser) {
				$this->rauthor = $finduser;
			} else {
			}
		}
		return $haveuser;
	}
	
	/**************
	* Setter for review text, uses string sanitiser method
	**************/
	private function setText($text) {$this->rtext=util::sanStr($text);}
	
	/**************
	* Remove all current reviews in reviews array
	**************/
	public function clearReviews() {$this->reviews=[];}
	
	/**************
	* Class getters
	**************/
	public function getRID() { return $this->rid; }
    public function getMID() { return $this->mid; }
	public function getRDate() { return $this->rdate; }
	public function getAuthor() { return $this->rauthor; }
	public function getText() { return $this->rtext; }
	public function getRating() { return $this->rating; }
	
	/**************
	* Uses reviewCRUD to retrieve a recordset of reviews as an associative array
	* instantiates Review for each row and adds to the reviews array
	**************/
	public function getReviewsForMovie($mid) {
        $havecomments=false;
		$source=new ReviewCRUD();
		$data=$source->getReviewsForMovie($mid);
		if(count($data)>=1) {
            foreach ($data as $review) {
                $this->initReview($review);
            }
			$havecomments=true;
		}
		return $havecomments;
	}

	/**************
	* returns a review from a review ID ($rid)
	**************/	

    public function getReviewByID($rid) {
		$rid = (int)$rid;
        $havecomment = false;
        $source=new ReviewCRUD();
        $data=$source->getReviewByID($rid);
        if($data) {
            $this->initReview($data);
            $havecomment=true;
        }
		return $havecomment;
    }
	
	/**************
	* sets reviews attributes from a retrieved associative array of reviews
	**************/
	public function initReview($movie) {
		$this->setRID($movie["reviewID"]);
        $this->setMID($movie["movieID"]);
		$this->setRDate($movie["reviewTime"]);
		$this->setAuthor($movie["reviewPoster"]);
		$this->setText($movie["reviewText"]);
		$this->setRating($movie["userRating"]);
	}
	
	/**************
	* Accepts 4 input parameters to create a new Review
	* Author is set using setAuthor, accepts an id or instance of author
	* Calls reviewCrud addReview and getLastUserReview to add new review
	* if successful review ID and time set from database
	* returns review ID if successful, 0/false if unsuccessful with an
	* error message as an associative array
	**************/	
public function addReview($author, $text, $movieID, $rating) {
    $rid = 0;
    $insert = 0;
    $messages = "";

    if (!is_int($author) && !$author instanceof User) {
        return ['insert' => 0, 'messages' => "Invalid author format."];
    }

    if ($this->setAuthor($author)) {
        $target = new ReviewCRUD();
        $this->setText($text);

        $insert = $target->addReview($this->getText(), $this->getAuthor()->getUserid(), $movieID, $rating);

        if ($insert !== 1) { 
            $messages .= "Add review failed."; 
            $insert = 0;
        } else {
            $resultset = $target->getLastUserReview($this->getAuthor()->getUserid());
            if (count($resultset) == 1) {
                $this->setRID($resultset[0]["reviewID"]);
                $this->setMID($resultset[0]["movieID"]);
                $this->setRDate($resultset[0]["reviewTime"]);
				$this->setRating($resultset[0]["userRating"]);
                $insert = 1;
            } else {
                $messages .= "Failed to retrieve the last added review.";
            }
        }
    } else { 
        $messages = "Invalid Poster<br>"; 
    }

    return ['insert' => $insert, 'messages' => $messages];
}


	/**************
	* updates a review when a user edits a review
	**************/	

	public function updateReview($content, $rid, $rating) {
		$messages = "";
		$update = 0;
		$found = $this->getReviewById($rid);
		$target = new ReviewCRUD();
	
		if ($found) {
			if (util::posted($content)) {
				$messages .= $this->setText($content);
			}
			if ($messages == "") {
				$updateResult = $target->updateReview($this->getText(), $rid, $rating);
				$update = $updateResult['update'];
				$messages = $updateResult['messages'];
			}
		} else {
			$messages = "Review not found.";
		}
	
		$result = ['update' => $update, 'messages' => $messages];
		return $result;
	}
		
	/**************
	* deletes a review based on a review ID ($rid)
	**************/	
	public function deleteReview($rid) {
		$messages = "";
		$delete = 0;
		$found = $this->getReviewById($rid); 
		$target = new ReviewCRUD();
	
		if ($found) {
			$deleteResult = $target->deleteReview($rid);
			$delete = $deleteResult['delete'];
			$messages = $deleteResult['messages'];
		} else {
			$messages = "Review not found.";
		}
	
		return ['delete' => $delete, 'messages' => $messages];
	}


	/**************
	* returns reviews in an array, for use when formatting a movie into an array
	**************/	
	public function toArray() {
		$reviews=[];
		
		foreach($this->reviews as $review) {
			array_push($reviews, $review->toArray());
		}
		
		$outarray=array(
			'rid' => htmlentities($this->getRID()),
			'mid' => htmlentities($this->getMID()),
			'date' => htmlentities($this->getRDate()->format("Y-m-d H:i:s")),
			'username' => htmlentities($this->getAuthor()->getUsername()),
			'content' => htmlentities($this->getText()) 
		);
		return $outarray;
	}

}

?>
