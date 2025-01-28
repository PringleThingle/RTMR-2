<?php
require_once("db.php");

/*****************
* Article CRUD class
*****************/
class ArticleCRUD {
	/*************
	* Standard constructor and database connection features
	*************/
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}
	
	/**************
	* Returns an associative array where blogID = aid
	* will return an empty array if no article found
	**************/	
	public function getArticleById($aid, $style=MYSQLI_ASSOC) {
		$this->sql="select * from blogarticle where blogID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$aid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;		
	}

	/**************
	* Returns an associative array for the last article added
	* will return an empty array if no article found
	**************/		
	public function getLastArticle($style=MYSQLI_ASSOC) {
		$this->sql="select * from blogarticle order by blogtime desc limit 1";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* Returns an associative array where blogposter = poster
	* ordered by the blogtime DESC limited to 1 article
	* will return an empty array if no article found
	**************/		
	public function getLastUserArticle($poster, $style=MYSQLI_ASSOC) {
		$this->sql="select * from blogarticle where blogposter=? order by blogtime desc limit 1";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$poster);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
	
	/**************
	* A general purpose method for getting multiple articles,
	* Returns an associative array of articles
	* @param start A starting date for the query
	* @param qty The maximum number of articles to return
	* @param direction optional default DESC - compared against a whitelist to prevent injection
	* @param style optional default MYSQLI_ASSOC - type of array to return for query
	**************/	
	public function getArticles($start,$qty,$direction='DESC',$style=MYSQLI_ASSOC) {
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
		$this->sql="select * from blogarticle where blogtime $comparator ? order by blogtime $direction limit ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("si",$start,$qty);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;			
	}

	/**************
	* adds a new Article, returns 1 on success, error message on fail
	**************/		
	public function addArticle($title, $text, $poster) {
		$this->sql="insert into blogarticle (articletitle,articletext,blogposter) values (?,?,?);";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ssi",$title,$text,$poster);
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			return "Could not add article<br />";
		} else {
			return $this->stmt->affected_rows;
		}		
	}

	/**************
	* updates existing article, returns 1 on success, error message on fail
	**************/	

	public function updateArticle($title, $text, $aid) {
		$this->sql = "UPDATE blogarticle SET articletitle=?, articletext=? WHERE blogID=?;";
		$this->stmt = self::$db->prepare($this->sql);
	
		if (!$this->stmt) {
			return ['update' => 0, 'messages' => self::$db->error];
		}
	
		$this->stmt->bind_param("ssi", $title, $text, $aid);
		$result = $this->stmt->execute();
	
		if ($result) {
			return ['update' => 1, 'messages' => "Update successful"];
		} else {
			return ['update' => 0, 'messages' => $this->stmt->error];
		}
	}
	/**************
	* Deletes existing article, returns 1 on success, error message on fail
	**************/		
	public function deleteArticle($aid) {
		$this->sql = "DELETE FROM blogarticle WHERE blogID=?;";
		$this->stmt = self::$db->prepare($this->sql);

		if (!$this->stmt) {
			return ['update' => 0, 'messages' => self::$db->error];
		}
	
		$this->stmt->bind_param("i", $aid);
		$result = $this->stmt->execute();
	
		if ($result) {
			return ['update' => 1, 'messages' => "Delete successful"];
		} else {
			return ['update' => 0, 'messages' => $this->stmt->error];
		}

	}

}

?>



