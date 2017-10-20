<?php 


class user {
	
	private $isTeacher = false;
	private $isPupil = false;
	private $isEltern = false;
	
	private $isNone = true;
	
	private $teacherObject = null;
	private $pupilObject = null;
	private $elternObject = null;
	
	
	private $data;
	
	public function __construct($data) {
		$this->data = $data;
				
		
		if($this->data['userIsLehrer'] > 0) {
		    $this->isTeacher = true;
			$this->teacherObject = lehrer::getByASVId($this->data['userLehrerAsvID']);
		}
		
		if($this->data['userIsSchueler'] > 0) {
		    $this->isPupil = true;
		    $this->pupilObject = schueler::getByAsvID($this->data['userSchuelerAsvID']);
		}
		
		if($this->data['userIsEltern'] > 0) {
		    $this->isEltern = true;
		    $this->elternObject = new eltern(explode(",",$this->data['userElternSchuelerAsvIDs'])); 
		   
		}
	}
	
	public function isSekretariat() {
	    return $this->data['userIsSekretariat'] > 0;
	}
	
	public function getUserName() {
		return $this->data['userName'];
	}
	
	public function getUserID() {
		return $this->data['userID'];
	}
	
	public function getData($key) {
		return $this->data[$key];
	}
	
	public function isPupil() {
		return $this->isPupil;
	}
	
	public function isTeacher() {
		return $this->isTeacher;
	}
	
	public function isEltern() {
		return $this->isEltern;
	}

	public function isAdmin() {
		return $this->data['userIsAdmin'];
	}
	
	/**
	 * 
	 * @return lehrer
	 */
	public function getTeacherObject() {
		return $this->teacherObject;
	}
	
	public function getPupilObject() {
		return $this->pupilObject;
	}
	
	public function getElternObject() {
		return $this->elternObject;
	}
	
	public function getDisplayName() {
		return $this->data['userFirstName'] . " " . $this->data['userLastName'];
	}
	
	
	/**
	 * 
	 * @param int $userID
	 * @return user|NULL
	 */
	public static function getUserByID($userID) {
		$data = DB::getDB()->query_first("SELECT * FROM users WHERE userID='" . $userID . "'");
		if($data['userID'] > 0) {
			return new user($data);
		}
		
		return null;
	}
	
	
	/**
	 * 
	 * @return user[]
	 */
	public static function getAll() {
		$data = DB::getDB()->query("SELECT * FROM users");
		
		$all = [];
		while($u = DB::getDB()->fetch_array($data)) {
			$all[] = new user($u);
		}
		
		return $all;
	}
}
