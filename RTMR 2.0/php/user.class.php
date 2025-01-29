<?php
require_once("util.class.php");
require_once("usercrud.class.php");
require_once("userhash.class.php");
require_once("dob.class.php");

class User {

	/***************
	* userid - serial user id
	* username - username of user
	* userhash - hashed password of a user
	* firstname - first name of a user
	* surname - surname of a user
	* lastsession - the last session a user had
	* email - email address of a user (not used here)
	* dob - user date of birth
	* usertype - type of user (user, anonymous, suspended or admin)
	***************/
	private $userid, $username,$userhash, $firstname, $surname, $lastsession, $email, $dob, $usertype;
	
	/**************
	* Creates a default user, in this implementation users are
	* instantiated with default values, then initialised using setters below
	**************/
	public function __construct() {
		$this->userid=-1;
		$this->username="Anon";
		$this->usertype=0;
		$this->userhash=new UserHash();
		$this->dob = new DOB();
	}
	
	/**************
	* class setters
	**************/
	private function setUserid($userid) {
		$this->userid=$userid;
	}
	
	private function setUsername($username) {
		$message="";
		if(util::valUName($username)) {
			$this->username=$username;
		} else {$message="Invalid Username<br />";}
		return $message;	
	}
	
	private function setFirstname($firstname) {
		$message="";
		if(util::valStr($firstname)) {
			$this->firstname=$firstname;
		} else {$message="Invalid Firstname<br />";}
		return $message;
	}
	
	private function setSurname($surname) {
		$message="";
		if(util::valStr($surname)) {
			$this->surname=$surname;
		} else {$message="Invalid Surname<br />";}
		return $message;
	}
	
	private function setEmail($email) {
		$message="";
		if(util::valEmail($email)) {
			$this->email=$email;
		} else {$message="Invalid Email Address<br />";}
		return $message;
	}
	
	private function setDOB($dob) {
		$message="";
		if(!$this->dob->setDOB($dob))
		{ $message.="Date is not correct<br />"; }
		if($this->dob->getAge()<16)
		{ $message.= "User must be 16 years or older<br />";}	
		return $message;
	}
	
	private function setSession($session) {
		$this->lastsession=$session;
	}
	
	private function setUsertype($usertype) {
		$this->usertype=$usertype;
	}
	
	private function setPass($password) {
		$message="";
		if($this->userhash->checkRules($password)) {
			$this->userhash->newHash($password);
		} else {
			$message="Password did not meet complexity standards<br />";
		}
		return $message;
	}

	/**************
	* Class getters
	**************/
	public function getUserid() { return $this->userid; }
	public function getUsername() { return $this->username; }
	public function getFirstname() { return $this->firstname; }
	public function getSurname() { return $this->surname; }
	public function getEmail() { return $this->email; }
	public function getDOB($format="Y-m-d") { return $this->dob->format($format); }
	public function getSession() { return $this->lastsession; }
	public function getUsertype() { return $this->usertype; }

	public function getUserByName($username) {
		$haveuser=false;
		$source=new UserCRUD();
		$data=$source->getUserByName($username);
		if(count($data)==1) {
			$user=$data[0];
			$this->setUserid($user["userid"]);
			$this->setUsername($user["username"]);
			$this->setFirstname($user["firstname"]);
			$this->setSurname($user["surname"]);
			$this->setSession($user["lastsession"]);
			$this->setEmail($user["email"]);
			$this->setDOB($user["dob"]);
			$this->setUsertype($user["usertype"]);
			$this->userhash->initHash($user["userpass"]);
			$haveuser=true;
		}
		return $haveuser;
	}	
	
	public function getUserById($userid) {
		$haveuser=false;
		$source=new UserCRUD();
		$data=$source->getUserById($userid);
		if(count($data)==1) {
			$user=$data[0];
			$this->setUserid($user["userid"]);
			$this->setUsername($user["username"]);
			$this->setFirstname($user["firstname"]);
			$this->setSurname($user["surname"]);
			$this->setSession($user["lastsession"]);
			$this->setEmail($user["email"]);
			$this->setDOB($user["dob"]);
			$this->setUsertype($user["usertype"]);
			$this->userhash->initHash($user["userpass"]);
			$haveuser=true;
		} 
		return $haveuser;
	}	
	
	/**************
	* used to check if a users username and password combination match to a user in the database.
	**************/
	public function authNamePass($username, $userpass) {
		$authenticated=$this->getUserByName($username);
		if($authenticated) {
			$authenticated=$this->userhash->testPass($userpass);
		}
		return $authenticated;
	}
	
	/**************
	* Saves a user session in the database
	**************/
	public function storeSession($userid, $session="") {
		$result=0;
		$target=new UserCRUD();
		$result=$target->storeSession($userid, $session);
		if($result) {$this->setSession($session);}
		return $result;
	}
	
	/**************
	* Authenticates a user using their ID and session
	**************/
	public function authIdSession($id, $session) {
		$authenticated=false;
		$authenticated=$this->getUserById($id);
		if($authenticated) {
			if($this->getSession()!=$session) { $authenticated=false; }
		}
		return $authenticated;
	}


	/**************
	* Registers a user with details they provided
	**************/
	public function registerUser($username,$password, $firstname,$surname, $email, $dob) {
		$insert=0;
		$messages="";
		$target=new UserCRUD();
		$messages.=$this->setUsername($username);
		$messages.=$this->setFirstname($firstname);
		$messages.=$this->setSurname($surname);
		$messages.=$this->setPass($password);
		$messages.=$this->setEmail($email);
		$messages.=$this->setDOB($dob);
		if($messages=="") {
			$insert=$target->storeNewUser($this->getUsername(),$this->getFirstname(),$this->getSurname(),$this->userhash->getHash(),$this->getEmail(), $this->getDOB());
			if($insert!=1) { $messages.=$insert;$insert=0; }
		}
		$result=['insert' => $insert,'messages' => $messages];
		return $result;
	}	

	/**************
	* Updates an existing users details
	**************/
	public function updateUser($username,$firstname,$surname,$password,$email,$dob,$usertype, $userid) {		
		$update=0;
		$messages="";
		$found=$this->getUserById($userid);
		$target=new UserCRUD();
		if($found) {
			if(util::posted($username)){$messages.=$this->setUsername($username);}
			if(util::posted($firstname)){$messages.=$this->setFirstname($firstname);}
			if(util::posted($surname)){$messages.=$this->setSurname($surname);}
			if(util::posted($password)){$messages.=$this->setPass($password);}
			if(util::posted($email)){$messages.=$this->setEmail($email);}
			if(util::posted($dob)){$messages.=$this->setDOB($dob);}
			if(util::posted($usertype)){$messages.=$this->setUsertype($usertype);}
			if($messages=="") {
				$update=$target->updateUser($this->getUsername(), $this->getFirstname(), $this->getSurname(), $this->userhash->getHash(), $this->getEmail(), $this->getDOB(),$this->getUsertype(), $userid);
				if($update!=1) {$messages=$update;$update=0;}
			}			
		}
		$result=['update' => $update, 'messages' => $messages];	
		return $result;
	}
	

	/**************
	* Returns a string representation of a user object
	**************/
	public function __toString() {
		$output="";
		$output.="User : ".$this->getUsername()."<br />";
		$output.="Name : ".$this->getFirstname()." ".$this->getSurname()."<br />";
		$output.="Email : ".$this->getEmail()."<br />";
		$output.="DOB : ".$this->getDOB()."<br />";
		$typedesc="Anonymous";
		switch($this->getUsertype()) {
			case 1: $typedesc="Suspended";
					break;
			case 2: $typedesc="User";
					break;
			case 3: $typedesc="Admin";
					break;
		}
		$output.="Account : ".$typedesc."<br />";
		return $output;
	}
}
?>