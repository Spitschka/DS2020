<?php 

class eltern {
	/**
	 * 
	 * @var schueler[]
	 */
	private $schueler = array();
	
	public function __construct($schuelerAsvIDs) {
		for($i = 0; $i < sizeof($schuelerAsvIDs); $i++) {
		    $schueler = schueler::getByAsvID($schuelerAsvIDs[$i]);
		    if($schueler != null) {
		        $this->schueler[] = schueler::getByAsvID($schuelerAsvIDs[$i]);
		    }
		}
	}
	
	public function getKlassenAsArray() {
		$klassen = array();
		
		for($i = 0; $i < sizeof($this->schueler); $i++) {
			$klassen[] = $this->schueler[$i]->getKlasse();
		}
		
		return $klassen;
	}
	
	/**
	 * 
	 * @return schueler[]
	 */
	public function getMySchueler() {
		return $this->schueler;
	}
}

