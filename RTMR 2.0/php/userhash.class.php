<?php

class UserHash {
	private $hashed;
	private $options=['cost' => 10,];
	
	public function __construct() {	}
	
	private function setHash($hash){$this->hashed=$hash;}
	public function getHash(){return $this->hashed;}

	public function initHash($hash) {
		$this->setHash($hash);
	}

	public function newHash($plaintext) {
		$this->setHash(password_hash($plaintext,PASSWORD_DEFAULT,$this->options));
	}
	
	public function testPass($plaintext) {
		return password_verify($plaintext,$this->getHash());
	}
	
	public function checkRules($password) {
		$valid=true;
		if(strlen(trim($password))<8 || strlen(trim($password))>72) {$valid=false;}
		return $valid;
	}
}
?>