<?php


class session {	
	private $data;
	private $groupNames = array();
	
	private $userObject = array();
	
	
	
	public function __construct($data=null) {
		$this->data = $data;
		

		$this->userObject = user::getUserByID($this->data['sessionUserID']);
		
		if($this->userObject == null) {
			$this->delete();
			header("Location: index.php");
			exit(0);
		}		
	}
	
	public function isSavedSession() {
		return $this->data['sessionType'] == "SAVED";
	}
	
	public function update() {
		DB::getDB()->query("UPDATE sessions SET sessionLastActivity=UNIX_TIMESTAMP(), sessionIP='" . $_SERVER['REMOTE_ADDR'] . "' WHERE sessionID='" . $this->data['sessionID'] . "'");
	}
	
	public static function cleanSessions() {
		DB::getDB()->query("DELETE FROM sessions WHERE sessionLastActivity < ".(time()-3600) . " AND sessionType='NORMAL'");
	}
	
	public function getData($index) {
		return $this->userObject->getData($index);
	}
	
	public function getMail() {
		return $this->userObject->getEMail();
	}
	
	public function getGroupNames() {
		return $this->userObject->getGroupNames();
	}
	
	public function isPupil() {
		return $this->userObject->isPupil();
	}
	
	public function getSessionID() {
		return $this->data['sessionID'];
	}
	
	public function isTeacher() {
		return $this->userObject->isTeacher();
	}
	
	public function isAdmin() {
		return $this->userObject->isAdmin();
	}

	public function isEltern() {
		return $this->userObject->isEltern();
	}
	
	public function delete() {
		DB::getDB()->query("DELETE FROM sessions WHERE sessionID='" . $this->data['sessionID'] ."'");
		setcookie("ds2020session", null);	// Cookie löschen
	}
	
	public function getUserID() {
		return $this->userObject->getUserID();
	}

	/**
	 * Aktuelles Benutzerobjekt
	 * @return user
	 */
	public function getUser() {
		return $this->userObject;
	}
	
	public function getTeacherObject() {
		if($this->isTeacher()) return $this->userObject->getTeacherObject();
		else throw new RuntimeException("Internal Error. Teacher Object not availible!");
	}
	
	public function getSchuelerObject() {
		if($this->isPupil()) return $this->userObject->getPupilObject();
		else throw new RuntimeException("Internal Error. Student Object not availible!");
	}
	
	public function getPupilObject() {
		return $this->getSchuelerObject();
	}
	
	public function getElternObject() {
		if($this->isEltern())	return $this->userObject->getElternObject();
		else throw new RuntimeException("Internal Error. Parents Object not availible!");
	}
	
}