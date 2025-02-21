<?php
require_once("db.php");
class DirectorCRUD {
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}

	/**************
	* returns a directors information from their name
	**************/	
	public function getDirectorByName($name, $style=MYSQLI_ASSOC) {
		$this->sql="select * from directorInfo where directorName = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$name);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
	
	/**************
	* returns a directors information from their $directorid
	**************/	
	public function getDirectorByID($directorid, $style=MYSQLI_ASSOC) {
		$this->sql="select * from directorInfo where directorID = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$directorid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* returns a directors name from a $movieid
	**************/	
	public function getDirectorNameByMovieID($movieid) {
		$this->sql = "SELECT directorInfo.directorName 
					  FROM directorInfo 
					  JOIN movieInfo ON directorInfo.directorID = movieInfo.directorID 
					  WHERE movieInfo.movieID = ?";
		$this->stmt = self::$db->prepare($this->sql);
	
		if (!$this->stmt) {
			die("SQL Preparation Error: " . self::$db->error . "<br />");
		}
	
		$this->stmt->bind_param("i", $movieid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		
		if ($row = $result->fetch_assoc()) {
			return $row['directorName'];  
		} else {
			return "Unknown Director";  
		}
	}
	
	
	
	/**************
	* stores a new director in the database including their ID from TMDB, name, DOB and birthplace
	**************/	
	public function storeNewDirector($directorid,$name,$dob,$birthplace) {
		$this->sql="insert into directorInfo (directorID,directorName,directorDOB,directorBirthplace) values(?,?,?,?);";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("isss",$directorid,$name,$dob,$birthplace);
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			$errors="";
			if(strpos($this->stmt->error,'directorID')) {
				$errors.="Director already exists.<br />";
			}
			return $errors;
		} else {
			return $this->stmt->affected_rows;
		}
	}	

	/**************
	* Returns a list of all directors ordered by their name
	**************/	
	public function getAllDirectors($orderby="directorName", $style=MYSQLI_ASSOC) {

		switch ($orderby) {
			case "directorName": $order="directorName";
						break;
			case "directorID": $order="directorID";
						break;
			default: $order="directorName";
						break;
		}
		$this->sql="select directorID, directorName, directorDOB, directorBirthplace from directorInfo order by $order;";
		$this->stmt= self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result=$this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

	/**************
	* This function is used to store a new director if they don't exist
	or to return an existing director if they already exist in the database
	**************/	
	public function getOrCreateDirector($directorid, $name, $dob = null, $birthplace = null) {
		$existingDirector = $this->getDirectorByID($directorid);
		
		if (!empty($existingDirector)) {
			return $directorid;
		}
	
		$result = $this->storeNewDirector($directorid ?? 0, $name ?? 'Unknown', $dob ?? '1990-1-1', $birthplace ?? 'Unknown');
		
		if ($result === 1) {
			return $directorid;
		} else {
			throw new Exception("Failed to store director: $result");
		}
	}
	
}
?>