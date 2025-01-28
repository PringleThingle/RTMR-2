<?php
require_once("db.php");
class UserCRUD {
	private static $db;
	private $sql, $stmt;
	
	public function __construct() {
		self::$db = db::getInstance();
	}

	/**************
	* returns a users information from their $username
	**************/	
	public function getUserByName($username, $style=MYSQLI_ASSOC) {
		$this->sql="select * from usertable where username = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("s",$username);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
	
	/**************
	* returns a users information from their $userid
	**************/	
	public function getUserById($userid, $style=MYSQLI_ASSOC) {
		$this->sql="select * from usertable where userid = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("i",$userid);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
	

	/**************
	* stores a users session using their ID
	**************/	
	public function storeSession($id, $session) {
		$this->sql="update usertable set lastsession=? where userid=?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("si",$session,$id);
		$this->stmt->execute();
		return $this->stmt->affected_rows;
	}
	
	/**************
	* stores a new user in the database including their username, firstname, surname, hashed password, email and date of birth
	**************/	
	public function storeNewUser($username,$firstname,$surname,$hash,$email,$dob) {
		$this->sql="insert into usertable (username,firstname,surname,userpass,email,dob) values(?,?,?,?,?,?);";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ssssss",$username,$firstname,$surname,$hash,$email,$dob);
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			$errors="";
			if(strpos($this->stmt->error,'email')) {
				$errors.="Email address exists<br />";
			}
			if(strpos($this->stmt->error,'username')) {
				$errors.="Username exists<br />";
			}
			return $errors;
		} else {
			return $this->stmt->affected_rows;
		}
	}	

	/**************
	* Updates a users information in the database, used if a user has their details updated
	**************/	

	public function updateUser($username,$firstname,$surname,$hash,$email,$dob,$usertype, $userid) {
		$this->sql="update usertable set username=?, firstname=?, surname=?, userpass=?, email=?, dob=?, usertype=? where userid=?;";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ssssssii",$username,$firstname,$surname,$hash,$email,$dob,$usertype,$userid);		
		$this->stmt->execute();
		if($this->stmt->affected_rows!=1) {
			$errors="";
			if(strpos($this->stmt->error,'email')) {
				$errors.="Email address exists<br />";
			}
			if(strpos($this->stmt->error,'username')) {
				$errors.="Username exists<br />";
			}
			return $errors;
		} else {
			return $this->stmt->affected_rows;
		}

	}
	

	/**************
	* Returns a list of all users orders by username
	**************/	
	public function getAllUsers($orderby="username", $style=MYSQLI_ASSOC) {

		switch ($orderby) {
			case "username": $order="username";
						break;
			case "userid": $order="userid";
						break;
			case "surname": $order="surname";
						break;
			default: $order="username";
						break;
		}
		$this->sql="select userid, username, firstname, surname from usertable order by $order;";
		$this->stmt= self::$db->prepare($this->sql);
		$this->stmt->execute();
		$result=$this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}
	
	/**************
	* Used to check if a username of email already exists in the database
	**************/	
	public function testUserEmail($username, $email, $style=MYSQLI_ASSOC) {
		$this->sql="select * from usertable where username = ? or email = ?";
		$this->stmt = self::$db->prepare($this->sql);
		$this->stmt->bind_param("ss",$username, $email);
		$this->stmt->execute();
		$result = $this->stmt->get_result();
		$resultset=$result->fetch_all($style);
		return $resultset;
	}

}
?>