<?php 

class lehrer {
	
	/**
	 * Alle Lehrer (Caching)
	 * @var lehrer[]
	 */
	private static $allTeachers = [];
	
	private $isActive = true;
	
	private $data;
	public function __construct($data) {
		$this->data = $data;
	}

	public function isSchulleitung() {
	    return $this->data['lehrerIsSchulleitung'] > 0;
	}
	
	public function getKuerzel() {
		return $this->data['lehrerKuerzel'];
	}
	
	public function getName() {
		return $this->data['lehrerName'];
	}
	
	public function getVornamen() {
		return $this->data['lehrerVornamen'];
	}
	
	public function getRufname() {
		return $this->data['lehrerRufname'];
	}
	
	public function getGeschlecht() {
		return $this->data['lehrerGeschlecht'];
	}
	
	public function getZeugnisUnterschrift() {
		return $this->data['lehrerZeugnisunterschrift'];
	}
	
	public function getAsvID() {
		return $this->data['lehrerAsvID'];
	}
	
	public function getXMLID() {
		return $this->data['lehrerID'];
	}
	
	public function getMail() {
		if($this->data['lehrerUserID'] > 0) {
			$user = DB::getDB()->query_first("SELECT userEMail FROM users WHERE userID='" . $this->data['lehrerUserID'] . "'");
			
			if($user['userEMail'] != "") return $user['userEMail'];
			else return null;
		}
		else return null;
	}
	
	public function getKlassenMitKlasseleitung() {
		$grades = klasse::getAllKlassen();
		
		$result = array();
		
		for($i = 0; $i < sizeof($grades); $i++) {
			for($k = 0; $k < sizeof($grades[$i]->getKlassenLeitung()); $k++) {
				if($grades[$i]->getKlassenLeitung()[$k]->getKuerzel() == $this->getKuerzel()) {
					$result[] = $grades[$i];
					break;	// Nur einmal die Klasse aufführen
				}
			}
			
		}
		
		return $result;
	}
	
	public function isFachschaftsleitung() {
	    $faecher = fach::getAll();
	    for($i = 0; $i < sizeof($faecher); $i++) {
	        $result = $faecher[$i]->isFachschaftsleitung($this);
	       if($result) return true;
	    }
	    
	    return false;
	}
	
	public function getUserID() {
		return $this->data['lehrerUserID'];
	}

	public function getAmtsbezeichnung() {
		return $this->data['lehrerAmtsbezeichnung'];
	}
	
	public function getDisplayNameMitAmtsbezeichnung() {
		return $this->getRufname() . " "  . $this->getName() . ", " . $this->getAmtsbezeichnung();
	}
	
	
	public static function getAllKuerzel() {
		self::initCache();
		
		$data = array();
		for($i = 0; $i < sizeof(self::$allTeachers); $i++) {
			$data[] = self::$allTeachers[$i]->getKuerzel();
		}
		
		return $data;
	}
	
	public static function getAll() {
		self::initCache();
		
		$data = array();
		for($i = 0; $i < sizeof(self::$allTeachers); $i++) {
			$data[] = self::$allTeachers[$i];
		}
		
		return $data;
	}
	
	public static function getByKuerzel($kuerzel) {
		self::initCache();
		
		for($i = 0; $i < sizeof(self::$allTeachers); $i++) {
			if(self::$allTeachers[$i]->getKuerzel() == $kuerzel) {
				return self::$allTeachers[$i];
			}
		}
		
		return null;
	}
	
	public static function getByASVId($asvID) {
		self::initCache();
		
		for($i = 0; $i < sizeof(self::$allTeachers); $i++) {
			if(self::$allTeachers[$i]->getAsvID() == $asvID) {
				return self::$allTeachers[$i];
			}
		}
		
		return null;
	}
	
	public static function getByXMLID($xmlID) {
		self::initCache();
		
		for($i = 0; $i < sizeof(self::$allTeachers); $i++) {
			if(self::$allTeachers[$i]->getXMLID() == $xmlID) {
				return self::$allTeachers[$i];
			}
		}
		
		return null;
	}
	
	public static function getByNameAndGeschlecht($name, $geschlecht) {
		self::initCache();
		
		for($i = 0; $i < sizeof(self::$allTeachers); $i++) {
			if(self::$allTeachers[$i]->getName() == $name) { // && self::$allTeachers[$i]->getGeschlecht() == $geschlecht) {
				return self::$allTeachers[$i];
			}
		}
		
		return null;
	}
	
	private static function initCache() {
		if(sizeof(self::$allTeachers) == 0) {
			$lehrer = DB::getDB()->query("SELECT * FROM lehrer ORDER BY lehrerName ASC, lehrerRufname ASC");
			
			$data = array();
			while($l = DB::getDB()->fetch_array($lehrer)) {
    			$l = new lehrer($l);
				
				self::$allTeachers[] = $l;
			}
		}
	}
	
	
}


?>