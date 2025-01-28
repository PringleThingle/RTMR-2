<?php

require_once("articlecrud.class.php");
require_once("user.class.php");
require_once("commentcrud.class.php");
require_once("util.class.php");


/**************
* Article class
**************/
class Comment {
	/***************
	* aid - serial article id
	* cid - serial comment id
	* cdate - instance of DateTime, when comment was created
	* cauthor - comment author, instance of User
	* ctext - main body of article textdomain
	* comments - an array for instances of Comment
	***************/
	private $aid, $cid, $cdate, $cauthor,$ctext;
	private $comments=[];
	
	/**************
	* Creates a 'blank' comment, in this implementation comments are
	* instantiated blank, then initialised using initComment
	* initComment is called by one of the methods which work with
	* the commentCRUD class. This has been done largely to cope with
	* PHP's inability to handle function overloading for constructors
	**************/
	public function __construct() {
		$this->cid=-1;
        $this->aid=-1;
		$this->cdate=new DateTime();
		$this->cauthor=new User();
		$this->ctext="";
	}
	
	/**************
	* Setter for Comment ID
	**************/
    private function setCID($cid){ $this->cid=$cid; }
	private function setAID($aid){ $this->aid=$aid; }
	
	/**************
	* Setter for datetime, accepts a date in string format
	**************/	
	private function setCDate($date) { 
		$this->cdate=DateTime::createFromFormat("Y-m-d H:i:s", $date);
		}
		
	/**************
	* Setter for Author, accepts either an instance of User
	* or an integer for the User's ID and creates an instance
	* returns true if instance of User or User is found by ID
	**************/

	private function setAuthor($author) {
		$haveuser = false;
		if ($author instanceof User) {
			$this->cauthor = $author;
			$haveuser = true;
		} else {
			$finduser = new User();
			$haveuser = $finduser->getUserById($author);
			if ($haveuser) {
				$this->cauthor = $finduser;
			} else {
			}
		}
		return $haveuser;
	}
	
	/**************
	* Setter for comment text, uses string sanitiser method
	**************/
	private function setText($text) {$this->ctext=util::sanStr($text);}
	
	/**************
	* Remove all current comments in comments array
	**************/
	public function clearComments() {$this->comments=[];}
	
	/**************
	* Class getters
	**************/
	public function getCID() { return $this->cid; }
    public function getAID() { return $this->aid; }
	public function getCDate() { return $this->cdate; }
	public function getAuthor() { return $this->cauthor; }
	public function getText() { return $this->ctext; }
	
	/**************
	* Uses commentCRUD to retrieve a recordset of comments as an associative array
	* instantiates Comment for each row and adds to the comments array
	**************/
	public function getCommentsForArticle($aid) {
        $havecomments=false;
		$source=new CommentCRUD();
		$data=$source->getCommentsForArticle($aid);
		if(count($data)>=1) {
            foreach ($data as $comment) {
                $this->initComment($comment);
            }
			$havecomments=true;
		}
		return $havecomments;
	}

	/**************
	* returns a comment from a comment ID ($cid)
	**************/	

    public function getCommentByID($cid) {
		$cid = (int)$cid;
        $havecomment = false;
        $source=new CommentCRUD();
        $data=$source->getCommentByID($cid);
        if($data) {
            $this->initComment($data);
            $havecomment=true;
        }
		return $havecomment;
    }
	
	/**************
	* sets comment's attributes from a retrieved associative array of comments
	**************/
	public function initComment($article) {
		$this->setCID($article["commentID"]);
        $this->setAID($article["blogID"]);
		$this->setCDate($article["commenttime"]);
		$this->setAuthor($article["commentposter"]);
		$this->setText($article["commenttext"]);	
	}
	
	/**************
	* Accepts 3 input parameters to create a new Comment
	* Author is set using setAuthor, accepts an id or instance of author
	* Calls commentCrud addComment and getLastUserComment to add new comment
	* if successful comment ID and time set from database
	* returns comment ID if successful, 0/false if unsuccessful with an
	* error message as an associative array
	**************/	
public function addComment($author, $text, $blogID) {
    $cid = 0;
    $insert = 0;
    $messages = "";

    if (!is_int($author) && !$author instanceof User) {
        return ['insert' => 0, 'messages' => "Invalid author format."];
    }

    if ($this->setAuthor($author)) {
        $target = new CommentCRUD();
        $this->setText($text);

        $insert = $target->addComment($this->getText(), $this->getAuthor()->getUserid(), $blogID);

        if ($insert !== 1) { 
            $messages .= "Add comment failed."; 
            $insert = 0;
        } else {
            $resultset = $target->getLastUserComment($this->getAuthor()->getUserid());
            if (count($resultset) == 1) {
                $this->setCID($resultset[0]["commentID"]);
                $this->setAID($resultset[0]["blogID"]);
                $this->setCDate($resultset[0]["commenttime"]);
                $insert = 1;
            } else {
                $messages .= "Failed to retrieve the last added comment.";
            }
        }
    } else { 
        $messages = "Invalid Poster<br>"; 
    }

    return ['insert' => $insert, 'messages' => $messages];
}


	/**************
	* updates a comment when a user edits a comment
	**************/	

	public function updateComment($content, $cid) {
		$messages = "";
		$update = 0;
		$found = $this->getCommentById($cid);
		$target = new CommentCRUD();
	
		if ($found) {
			if (util::posted($content)) {
				$messages .= $this->setText($content);
			}
			if ($messages == "") {
				$updateResult = $target->updateComment($this->getText(), $cid);
				$update = $updateResult['update'];
				$messages = $updateResult['messages'];
			}
		} else {
			$messages = "Comment not found.";
		}
	
		$result = ['update' => $update, 'messages' => $messages];
		return $result;
	}
		
	/**************
	* deletes a comment based on a comment ID ($cid)
	**************/	
	public function deleteComment($cid) {
		$messages = "";
		$delete = 0;
		$found = $this->getCommentById($cid); 
		$target = new CommentCRUD();
	
		if ($found) {
			$deleteResult = $target->deleteComment($cid);
			$delete = $deleteResult['delete'];
			$messages = $deleteResult['messages'];
		} else {
			$messages = "Comment not found.";
		}
	
		return ['delete' => $delete, 'messages' => $messages];
	}


	/**************
	* returns comments in an array, for use when formatting an article into an array
	**************/	
	public function toArray() {
		$comments=[];
		
		foreach($this->comments as $comment) {
			array_push($comments, $comment->toArray());
		}
		
		$outarray=array(
			'cid' => htmlentities($this->getCID()),
			'aid' => htmlentities($this->getAID()),
			'date' => htmlentities($this->getCDate()->format("Y-m-d H:i:s")),
			'username' => htmlentities($this->getAuthor()->getUsername()),
			'content' => htmlentities($this->getText()) 
		);
		return $outarray;
	}

}

?>
