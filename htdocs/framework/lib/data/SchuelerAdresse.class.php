<?php 


class SchuelerAdresse {
	private $data = array();
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	public function getID() {
		return $this->data['adresseID'];
	}
	
	public function getSchuelerAsvID() {
		return $this->data['adradresseSchuelerAsvIDesseID'];
	}
	
	public function isErziehungsberechtigter() {
		return $this->data['adresseWessen'] == 'eb';
	}
	
	public function isWeitererErziehungsberechtigter() {
		return $this->data['adresseWessen'] == 'web';
	}
	
	public function isSchueler() {
		return $this->data['adresseWessen'] == 's';
	}
	
	public function isWeiterer() {
		return $this->data['adresseWessen'] == 'w';
	}
	
	public function isAuskunftsberechtigt() {
		return $this->data['adresseIsAuskunftsberechtigt'] == '1';
	}
	
	public function isHauptansprechpartner() {
		return $this->data['adresseIsHauptansprechpartner'] == '1';
	}
	
	public function getStrasse() {
		return $this->data['adresseStrasse'];
	}
	
	public function getNummer() {
		return $this->data['adresseNummer'];
	}
	
	public function getOrt() {
		return $this->data['adresseOrt'];
	}
	
	public function getPLZ() {
		return $this->data['adressePostleitzahl'];
	}
	
	public function getFamilienname() {
		return $this->data['adresseFamilienname'];
	}
	
	public function getVorname() {
		return $this->data['adresseVorname'];
	}
	
	
	public function getAdresseAsText() {
		return 
			($this->isHauptansprechpartner() ? ("<u>Hauptansprechpartner</u><br />") : (""))
			
			. (($this->isErziehungsberechtigter()) ? ("Adresse Erziehungsberechtigter") : ("")) . 
			 (($this->isWeitererErziehungsberechtigter()) ? ("Adresse weiterer Erziehungsberechtigter") : ("")) .
			 (($this->isSchueler()) ? ("Adresse Schüler") : ("")) .
			 (($this->isWeiterer()) ? ("Adresse Weitere Person") : ("")) . "<br />" . 
			
			$this->getVorname() . " " . $this->getFamilienname() . "<br />" . 
			$this->getStrasse() . " " . $this->getNummer() . "<br />" . 
			$this->getPLZ() . " " . $this->getOrt()
		
			. "<br />" . 
			
			(($this->isAuskunftsberechtigt()) ? ("<font color=\"green\">Auskunftsberechtigt</font>") : ("<font color=\"red\">Nicht Auskunftsberechtigt</font>"))
		;
		
	}
	
	public function getAdresseAnschriftMitAuskunft() {
		return ($this->isHauptansprechpartner() ? ("<u>Hauptansprechpartner</u><br />") : (""))
		
		. (($this->isErziehungsberechtigter()) ? ("Adresse Erziehungsberechtigter") : ("")) .
		(($this->isWeitererErziehungsberechtigter()) ? ("Adresse weiterer Erziehungsberechtigter") : ("")) .
		(($this->isSchueler()) ? ("Adresse Schüler") : ("")) .
		(($this->isWeiterer()) ? ("Adresse Weitere Person") : ("")) . "<br />" .
		
		nl2br($this->getAnschrifttext()) . "<br />" . 
		$this->getStrasse() . " " . $this->getNummer() . "<br />" .
		$this->getPLZ() . " " . $this->getOrt() . 
		"<br /><br />" . 
		
		(($this->isAuskunftsberechtigt()) ? ("<font color=\"green\">Auskunftsberechtigt</font>") : ("<font color=\"red\">Nicht Auskunftsberechtigt</font>"));
	}
	
	public function getAdresseAnschrift() {
		return $this->getAnschrifttext() . "\r\n" .
		$this->getStrasse() . " " . $this->getNummer() . "\r\n" .
		$this->getPLZ() . " " . $this->getOrt();
	}
	
	
	
	/**
	 * Läd diese Adresse als Google Maps Link
	 * @return string SuchString für Google Maps
	 */
	public function getGoogleMapsQuery() {
		return urlencode($this->getStrasse() . " " . $this->getNummer() . " " . $this->getPLZ() . " " . $this->getOrt());
	}
	
}


?>