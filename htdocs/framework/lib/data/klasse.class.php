<?php 


class klasse {
	private static $klassen = array();
	
	private $klassenName = "";
	private $anzahlSchueler = 0;
	private $klassenleitung = array();
	private $schueler = array();
	
	
	public function __construct($klassenname, $anzahlSchueler) {
		$this->klassenName = $klassenname;
		$this->anzahlSchueler = $anzahlSchueler;
		

	}
	
	public function getKlassenName() {
		return $this->klassenName;
	}
	
	public function getKlassenstufe() {
		$data = DB::getDB()->query_first("SELECT DISTINCT schuelerJahrgangsstufe FROM schueler WHERE schuelerKlasse='" . $this->klassenName . "' AND schuelerJahrgangsstufe != ''");
		
		return $data[0];
	}

	/**
	 * Liest alle Ausbildungsrichtungen
	 * @return String[]
	 */
	public function getAusbildungsrichtungen() {
		$data = DB::getDB()->query("SELECT DISTINCT schuelerAusbildungsrichtung FROM schueler WHERE schuelerKlasse='" . $this->klassenName . "'");
		
		$ausb = [];
		
		while($a = DB::getDB()->fetch_array($data)) {
			$ausb[] = $a[0];
		}
		
		return $ausb;
	}
	
	
	
	/**
	 * Ermittelt die Klassenleitung der Klasse
	 * @return lehrer[] Lehrerobjekte, die Klassenleitungen sind
	 */
	public function getKlassenLeitung() {
		if(sizeof($this->klassenleitung) == 0) {
			$kDB = DB::getDB()->query("SELECT * FROM klassenleitung NATURAL JOIN lehrer WHERE klassenleitung.klasseName LIKE '" . $this->klassenName . "' ORDER BY klassenleitungArt ASC");
			while($k = DB::getDB()->fetch_array($kDB)) {
				
				$this->klassenleitung[] = new lehrer($k);
			}
		}
		
		return $this->klassenleitung;
	}
	
	public function isKlassenleitung($lehrer) {
	    $kls = $this->getKlassenLeitung();
	    
	    for($i = 0; $i < sizeof($kls); $i++) {
	        if($kls[$i]->getAsvID() == $lehrer->getAsvID()) return true;
	    }
	    
	    return false;
	}
	
	/**
	 * Überprüft, ob der Lehrer erste Klassenleitung ist.
	 * @param lehrer $lehrer
	 * @return bool janein
	 */
	public function isFirstKlassenleitung($lehrer) {
		$kDB = DB::getDB()->query("SELECT * FROM klassenleitung NATURAL JOIN lehrer WHERE klassenleitung.klasseName LIKE '" . $this->klassenName . "'");
		while($k = DB::getDB()->fetch_array($kDB)) {
			if($k['lehrerAsvID'] == $lehrer->getAsvID()) {
				return $k['klassenleitungArt'] == 1;
			}
		}
		
		return false;
	}
	
	
	/**
	 * 
	 * @return schueler[] Schueler der Klasse
	 */
	public function getSchueler($withAusgetretene=true) {
		if(sizeof($this->schueler) == 0) {
			if($withAusgetretene) $schuelerSQL = DB::getDB()->query("SELECT * FROM schueler WHERE schuelerKlasse='" . $this->klassenName . "' ORDER BY schuelerName ASC, schuelerRufname ASC");
			else $schuelerSQL = DB::getDB()->query("SELECT * FROM schueler WHERE schuelerKlasse='" . $this->klassenName . "' AND (schuelerAustrittDatum IS NULL OR schuelerAustrittDatum > CURDATE()) ORDER BY schuelerName ASC, schuelerRufname ASC");
				
			while($s = DB::getDB()->fetch_array($schuelerSQL)) {
		
				$this->schueler[] = new schueler($s);
			}
		}
		
		return $this->schueler;
		
	}
	
	/**
	 * @return klasse[] Klassen
	 */
	public static function getAllKlassen() {
		if(sizeof(self::$klassen) == 0) {
			$klassen = DB::getDB()->query("SELECT DISTINCT s1.schuelerKlasse, (SELECT COUNT(s2.schuelerAsvID) FROM schueler AS s2 WHERE s2.SchuelerKlasse=s1.schuelerKlasse) AS anzahlSchueler FROM schueler as s1 ORDER BY LENGTH(s1.schuelerKlasse), s1.schuelerKlasse ASC");
			while($klasse = DB::getDB()->fetch_array($klassen)) {
				self::$klassen[] = new klasse($klasse['schuelerKlasse'],$klasse['anzahlSchueler']);
			}
		}
		
		return self::$klassen;
	}
	
	public static function getAnzahlKlassen() {
		$a = DB::getDB()->query("SELECT DISTINCT schuelerKlasse FROM schueler");
	
		return DB::getDB()->num_rows($a);
	}
	
	public static function getByName($name) {
		
		$alle = self::getAllKlassen();
				
		for($i = 0; $i < sizeof($alle); $i++) {
			if($alle[$i]->getKlassenName() == $name) {
				return $alle[$i];
			}
		}
		
		return new klasse($name, 0);
	}
	
	public static function getByStundenplanName($stundenplan) {
		// TODO: Universell?
		
		$klassenname = $stundenplan;
		
		// Falls eine Ausbildungsrechnung angegeben ist, diese entfernen.
		if(strpos($stundenplan,"_") > 0) {
			$klassenname = substr($stundenplan, 0, strpos($stundenplan,"_"));
		}
		
		return self::getByName($klassenname);
	}
	
	public static function getByStundenplanKlassen($klassen) {
		$grades = [];
		
		for($i = 0; $i < sizeof($klassen); $i++) {
			$g = self::getByStundenplanName($klassen[$i]);
			if($g != null) $grades[] = $g;
		}
		
		return $grades;
	}
	
	/**
	 * 
	 * @param string $date Natural Date
	 */
	public function isAnwesend($date='') {
		
		
		if($date == '') $date = DateFunctions::getTodayAsNaturalDate();
		$tageAbwesend = DB::getSettings()->getValue('klassenabwesenheit_' . $this->getKlassenName());
		$tageAbwesend = explode("\n",$tageAbwesend);

		
		if(in_array($date, $tageAbwesend)) return false;
		else return true;
	}

	
	public static function getByUnterrichtForTeacher($teacher) {
		/**
		 * 
		 * @var klasse[] $klassen
		 */
		$klassen = [];
		
		$unterricht = SchuelerUnterricht::getUnterrichtForLehrer($teacher);
				
		for($i = 0; $i < sizeof($unterricht); $i++) {
			
			$klassenDesUnterrichts = $unterricht[$i]->getAllKlassen();
			
			for($g = 0; $g < sizeof($klassenDesUnterrichts); $g++) {
				$found = false;
				
				for($k = 0; $k < sizeof($klassen); $k++) {
					if($klassen[$k]->getKlassenName() == $klassenDesUnterrichts[$g]->getKlassenName()) {
						$found = true;
						break;
					}
				}
				
				if(!$found) {
					$klassen[] = $klassenDesUnterrichts[$g];
				}
			}
		}
		
		return $klassen;
	}

}


?>