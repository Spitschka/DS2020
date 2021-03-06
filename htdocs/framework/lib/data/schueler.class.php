<?php

class schueler {
	private static $all = array();
	private static $cachedAllElternUsers = [];

	private $data;

	private $adressen = array();
	private $telefonnummern = array();
	private $emailadressen = [];


	public function __construct($data) {
		$this->data = $data;
	}

	
	public function getKlasse() {
		if(substr($this->data['schuelerKlasse'],0,1) == "0") return substr($this->data['schuelerKlasse'],1);
		else return $this->data['schuelerKlasse'];
	}

	/**
	 * Klasse als String
	 * @return string
	 */
	public function getGrade() {
		return $this->getKlasse();
	}
	
	
	/**
	 * 
	 * @return NULL|klasse
	 */
	public function getKlassenObjekt() {
		return klasse::getByName($this->getKlasse());
	}

	public function getCompleteSchuelerName() {
		return $this->data['schuelerName'] . ", "  . $this->data['schuelerRufname'];
	}
	
	public function getGeschlecht() {
		return $this->data['schuelerGeschlecht'];
	}

	public function getID() {
		return $this->data['schuelerAsvID'];
	}

	public function getAsvID() {
		return $this->data['schuelerAsvID'];
	}

	public function getGeburtstagAsNaturalDate() {
		return DateFunctions::getNaturalDateFromMySQLDate($this->data['schuelerGeburtsdatum']);
	}
	
	public function getGeburtstagAsSQLDate() {
		return $this->data['schuelerGeburtsdatum'];
	}

	public function getName() {
		return ($this->data['schuelerName']);
	}

	public function getRufname() {
		return ($this->data['schuelerRufname']);
	}
	
	public function getVornamen() {
		return ($this->data['schuelerVornamen']);
	}
		
	public function delete() {
		DB::getDB()->query("DELETE FROM schueler WHERE schuelerAsvID='" . $this->getAsvID() . "'");
		DB::getDB()->query("DELETE FROM eltern_adressen WHERE adresseSchuelerAsvID='" . $this->getAsvID() . "'");	
		DB::getDB()->query("DELETE FROM eltern_email WHERE elternSchuelerAsvID='" . $this->getAsvID() . "'");
	}
	
	public function setFoto($fileUpload) {
		DB::getDB()->query("UPDATE schueler SET schuelerFoto='" . $fileUpload->getID() . "' WHERE schuelerAsvID='" . $this->getAsvID() . "'");
	}
	
	public function removeFoto() {
		DB::getDB()->query("UPDATE schueler SET schuelerFoto='0' WHERE schuelerAsvID='" . $this->getAsvID() . "'");
	}
	
	/**
	 * 
	 * @return FileUpload|NULL
	 */
	public function getFoto() {
		return FileUpload::getByID($this->data['schuelerFoto']);
	}


	public function getKlassenleitungAsText() {
		$klassen = klasse::getAllKlassen();
		for($i = 0; $i < sizeof($klassen); $i++) {
			if($klassen[$i]->getKlassenName() == $this->getKlasse()) {
				$kl = $klassen[$i]->getKlassenleitung();

				$text =  "";

				for($k = 0; $k < sizeof($kl); $k++) {
					$text .= (($k > 0) ? ", " : "") . $kl[$k]->getKuerzel();
				}

				return $text;
			}
		}


		return "";
	}

	public function isKlassenleitung($lehrerObjekt) {
		$klassen = klasse::getAllKlassen();

		for($i = 0; $i < sizeof($klassen); $i++) {
			if($klassen[$i]->getKlassenName() == $this->getKlasse()) {
				$kl = $klassen[$i]->getKlassenleitung();

				for($k = 0; $k < sizeof($kl); $k++) {
					if($lehrerObjekt->getKuerzel() == $kl[$k]->getKuerzel()) return true;
				}
			}
		}


		return false;
	}


	public function getAlter() {
		$currentDate = DateFunctions::getTodayAsSQLDate();
		list($cJahr, $cMonat, $cTag) = explode("-",$currentDate);
		list($sJahr, $sMonat, $sTag) = explode("-",$this->data['schuelerGeburtsdatum']);

		$alter = $cJahr - $sJahr;

		if($cMonat < $sMonat) return $alter-1;
		else if($cMonat > $sMonat) return $alter;
		else {
			if($cTag < $sTag) return $alter-1;
			else return $alter;
		}

		// UNreachable COde:
		return "Alter unbekannt";
	}

	public function getWohnort() {
		$this->initAdressen();

		for($i = 0; $i < sizeof($this->adressen); $i++) {
			if($this->adressen[$i]->isSchueler()) {
				return $this->adressen[$i]->getOrt();
			}
		}

		return "Wohnort unbekannt";
	}

	/**
	 * @return SchuelerAdresse[]
	 */
	public function getAdressen() {
		$this->initAdressen();

		return $this->adressen;
	}
	
	public function getSchuelerUserID() {
		return $this->data['schuelerUserID'];
	}

	/*
	 * @return array(SchuelerAdresse)
	 */
	public function getTelefonnummer() {
		$this->initAdressen();

		return $this->telefonnummern;
	}
	
	/**
	 * 
	 * @return SchuelerElternEmail[]
	 */
	public function getElternEMail() {
		$this->initAdressen();
		
		return $this->emailadressen;
	}

	private function initAdressen() {
		include_once("../framework/lib/data/SchuelerAdresse.class.php");
		include_once("../framework/lib/data/SchuelerTelefonnummer.class.php");
		if(sizeof($this->adressen) == 0) {
			$adressen = DB::getDB()->query("SELECT * FROM eltern_adressen WHERE adresseSchuelerAsvID='" . $this->data['schuelerAsvID'] . "' ORDER BY adresseIsHauptansprechpartner DESC");
			while($a = DB::getDB()->fetch_array($adressen)) {
				$this->adressen[] = new SchuelerAdresse($a);
			}

			$telefonnummern = DB::getDB()->query("SELECT * FROM eltern_telefon WHERE schuelerAsvID='" . $this->data['schuelerAsvID'] . "'");
			while($t = DB::getDB()->fetch_array($telefonnummern)) {
				$this->telefonnummern[] = new SchuelerTelefonnummer($t);
			}
			
			$telefonnummern = DB::getDB()->query("SELECT * FROM eltern_email WHERE elternSchuelerAsvID='" . $this->data['schuelerAsvID'] . "'");
			while($t = DB::getDB()->fetch_array($telefonnummern)) {
				$this->emailadressen[] = new SchuelerElternEmail($t);
			}
		}
	}

	/**
	 * @return schueler[] alle Schüler
	 */
	public static function getAll($orderBy='schuelerName, schuelerRufname') {
		if(sizeof(self::$all) == 0) {
			$alle = DB::getDB()->query("SELECT * FROM schueler ORDER BY $orderBy");
			while($s = DB::getDB()->fetch_array($alle)) {
				self::$all[] = new schueler($s);
			}
		}

		return self::$all;
	}
	
	public static function getAnzahlSchueler() {
		$a = DB::getDB()->query_first("SELECT COUNT(schuelerAsvID) AS a FROM schueler WHERE schuelerAustrittDatum IS NULL OR schuelerAustrittDatum < CURDATE()");
		return $a['a'];
	}
	
	public static function getAnzahlWeiblich() {
		$a = DB::getDB()->query_first("SELECT COUNT(schuelerAsvID) AS a FROM schueler WHERE (schuelerAustrittDatum IS NULL OR schuelerAustrittDatum < CURDATE()) AND schuelerGeschlecht='w'");
		return $a['a'];
	}
	
	public static function getAnzahlMaennlich() {
		$a = DB::getDB()->query_first("SELECT COUNT(schuelerAsvID) AS a FROM schueler WHERE (schuelerAustrittDatum IS NULL OR schuelerAustrittDatum < CURDATE()) AND schuelerGeschlecht='m'");
		return $a['a'];
	}

	public function isAusgetreten() {
		if($this->data['schuelerAustrittDatum'] != "") {
			$data = explode("-",$this->data['schuelerAustrittDatum']);
			$timeAustritt = mktime(1,1,1,$data[1],$data[2],$data[0]);
			return $timeAustritt < time();
		}
		else return false;
	}

	public function getAustrittDatumAsMySQLDate() {
		return $this->data['schuelerAustrittDatum'];
	}

	public static function getByAsvID($asvID) {
		
		$all = self::getAll();
		
		for($i = 0; $i < sizeof($all); $i++) {
			if($all[$i]->getAsvID() == $asvID) return $all[$i];
		}
		
		return null;

	}
	
	public function getUserName() {
		if($this->data['schuelerUserID'] > 0) {
			$user = DB::getDB()->query_first("SELECT userName FROM users WHERE userID='" . $this->data['schuelerUserID'] . "'");
			if($user['userName'] != "") return $user['userName'];
		}
		
		return null;
	}
	
	public function getUserID() {
		return $this->data['schuelerUserID'];
	}
	
	/**
	 * @return user[] Elternbenutzer zu diesem Schüler
	 */
	public function getParentsUsers() {
		
		if(sizeof(self::$cachedAllElternUsers) == 0) {
			$parents = DB::getDB()->query("SELECT * FROM eltern_email JOIN users ON eltern_email.elternEMail LIKE users.userName");
			
			while($p = DB::getDB()->fetch_array($parents)) {
				if(!is_array(self::$cachedAllElternUsers[$p['elternSchuelerAsvID']])) {
					self::$cachedAllElternUsers[$p['elternSchuelerAsvID']] = array();
				}
				self::$cachedAllElternUsers[$p['elternSchuelerAsvID']][] = new user($p);
			}
		}
		
		if(is_array(self::$cachedAllElternUsers[$this->getAsvID()])) {
			return self::$cachedAllElternUsers[$this->getAsvID()];
		}
		
		return [];
	}
}

?>