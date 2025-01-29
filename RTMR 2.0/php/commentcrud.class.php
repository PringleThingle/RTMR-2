<?php
require_once("db.php");
class CommentCRUD {
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	

	/**************
	* Returns an array of comments associated with a specific article ID ($aid)
	**************/	
	public function getCommentsForArticle($aid, $style=MYSQLI_ASSOC) {
		$this->sql="select * from commenttable where blogID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$aid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Returns a comment from a specific comment ID ($cid)
	**************/	
	public function getCommentById($cid, $style = MYSQLI_ASSOC) {
		$cid = (int)$cid;

		$this->sql = "SELECT * FROM commenttable WHERE commentID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		
		$this->stmt->bind_param("i", $cid);
	
		$this->stmt->execute();
	
		$result = $this->stmt->get_result();
	
		$resultset = $result->fetch_array($style);

		return $resultset ?: false;
	}

	/**************
	* adds a comment into the database, returns 1 on success, 0 on failure
	**************/	
    public function addComment($text, $poster, $blogID) {
        $this->sql = "INSERT INTO commenttable (commenttext, commentposter, blogID) VALUES (?, ?, ?)";
        $this->stmt = self::$db->prepare($this->sql);
    
        if (!$this->stmt) {
            return 0; 
        }
    
        $this->stmt->bind_param("sii", $text, $poster, $blogID);
    
        if (!$this->stmt->execute()) {
            return 0; 
        }
    
        return 1; 
    }

	/**************
	* Returns the last comment a specific user made
	**************/	
	public function getLastUserComment($poster, $style=MYSQLI_ASSOC) {
		$this->sql="select * from commenttable where commentposter=? order by commenttime desc limit 1";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$poster);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Returns the last comment that was made
	**************/	
    public function getLastComment($style=MYSQLI_ASSOC) {
		$this->sql="select * from commenttable order by commenttime desc limit 1";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Updates a comment in the database when a user edits 
	**************/	
	public function updateComment($text, $cid) {
		$this->sql = "UPDATE commenttable SET commenttext=? WHERE commentID=?;";
		$this->stmt = self::$db->prepare($this->sql);
	
		if (!$this->stmt) {
			return ['update' => 0, 'messages' => "Prepare failed: " . self::$db->error];
		}
	
		$this->stmt->bind_param("si", $text, $cid);
		$result = $this->stmt->execute();
	
		if ($result) {
			return ['update' => 1, 'messages' => "Update successful"];
		} else {
			return ['update' => 0, 'messages' => "Execution failed: " . $this->stmt->error];
		}
	}

	/**************
	* Deletes existing article, returns 1 on success, error message on fail
	**************/		
	public function deleteComment($cid) {
		$this->sql = "DELETE FROM commenttable WHERE commentID = ?;";
		$this->stmt = self::$db->prepare($this->sql);
	
		if (!$this->stmt) {
			return ['delete' => 0, 'messages' => "Prepare failed: " . self::$db->error];
		}
	
		$this->stmt->bind_param("i", $cid);
		$result = $this->stmt->execute();
	
		if ($result) {
			return ['delete' => 1, 'messages' => "Delete successful"];
		} else {
			return ['delete' => 0, 'messages' => "Execution failed: " . $this->stmt->error];
		}
	}
    
}

?>