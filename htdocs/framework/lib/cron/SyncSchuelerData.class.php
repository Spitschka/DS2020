<?php

class SyncSchuelerData extends AbstractCron {

	private $success = false;
	private $successText = '';
	
	public function __construct() {
	}

	public function execute() {
        $portalAllSchueler = PortalApi::getAllSchueler();
        $myAllSchueler = schueler::getAll();
        
        $deleted = 0;
        $added = 0;
        
        if(sizeof($portalAllSchueler) > 0) {
      
            // Alle Portalschüler noch im Mutterportal vorhanden?
            
            for($i = 0; $i < sizeof($myAllSchueler); $i++) {
                $found = false;
                for($s = 0; $s < sizeof($portalAllSchueler); $s++) {
                    if($myAllSchueler[$i]->getAsvID() == $portalAllSchueler[$s]->schuelerAsvID) {
                        $found = true;
                    }
                }
                
                if(!$found) {
                    $myAllSchueler[$i]->delete();
                    $deleted++;
                }
            }
            
            // Jetzt alle Schüler einfügen und bei Duplikaten updaten.
            
            DB::getDB()->query("TRUNCATE eltern_adressen");
            DB::getDB()->query("TRUNCATE eltern_telefon");
            DB::getDB()->query("TRUNCATE eltern_email");
            
            
            
            
            for($i = 0; $i < sizeof($portalAllSchueler); $i++) {
                $added++;
                
                if($portalAllSchueler[$i]->schuelerAustrittDatum != "") {
                    $austritt = "'" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerAustrittDatum) . "'";
                }
                else $austritt = 'null';
                
                DB::getDB()->query("INSERT INTO schueler 
                    (
                        schuelerAsvID,
                        schuelerName,
                        schuelerVornamen,
                        schuelerRufname,
                        schuelerGeschlecht,
                        schuelerGeburtsdatum,
                        schuelerKlasse,
                        schuelerJahrgangsstufe,
                        schuelerAustrittDatum
                     ) values (
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerAsvID) . "',
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerName) . "',
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerVornamen) . "',
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerRufname) . "',
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerGeschlecht) . "',
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerGeburtstag) . "',
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerKlasse) . "',
                        '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerJahrgangsstufe) . "',
                        $austritt
                     ) ON DUPLICATE KEY UPDATE
                        schuelerName = '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerName) . "',
                        schuelerVornamen = '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerVornamen) . "',
                        schuelerRufname = '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerRufname) . "',
                        schuelerGeschlecht = '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerGeschlecht) . "',
                        schuelerGeburtsdatum = '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerGeburtstag) . "',
                        schuelerKlasse = '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerKlasse) . "',
                        schuelerJahrgangsstufe = '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerJahrgangsstufe) . "',
                        schuelerAustrittDatum = $austritt
                
                ");
                
                for($a = 0; $a < sizeof($portalAllSchueler[$i]->adressen); $a++) {
                    DB::getDB()->query("INSERT INTO eltern_adressen
                        (
                            adresseSchuelerAsvID,
                            adresseWessen,
                            adresseIsAuskunftsberechtigt,
                            adresseIsHauptansprechpartner,
                            adresseStrasse,
                            adresseNummer,
                            adresseOrt,
                            adressePostleitzahl,
                            adresseFamilienname,
                            adresseVorname
                        ) values (
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerAsvID) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseWessen) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseIsAuskunftsberechtigt) . "',

                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseIsHauptansprechpartner) . "',

                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseStrasse) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseNummer) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseOrt) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adressePostleitzahl) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseFamilienname) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->adresseVorname) . "'
                        )
                    ");
                    
                    $adresseID = DB::getDB()->insert_id();
                    
                    for($t = 0; $t < sizeof($portalAllSchueler[$i]->adressen[$a]->telefonnummern); $t++) {
                        DB::getDB()->query("INSERT INTO eltern_telefon (
                            telefonNummer,
                            schuelerAsvID,
                            telefonTyp,
                            adresseID
                        ) values (
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->telefonnummern[$t]->nummer) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerAsvID) . "',
                            '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->telefonnummern[$t]->typ) . "',
                            '" . DB::getDB()->escapeString($adresseID) . "'
                        )");
                    }
                    
                    for($e = 0; $e < sizeof($portalAllSchueler[$i]->adressen[$a]->email); $e++) {
                        if($portalAllSchueler[$i]->adressen[$a]->email[$e]->adresse != "") {
                            DB::getDB()->query("INSERT INTO eltern_email (
                                elternEMail,
                                elternSchuelerAsvID,
                                elternAdresseID
                            ) values (
                                '" . DB::getDB()->escapeString($portalAllSchueler[$i]->adressen[$a]->email[$e]->adresse) . "',
                                '" . DB::getDB()->escapeString($portalAllSchueler[$i]->schuelerAsvID) . "',
                                '" . DB::getDB()->escapeString($adresseID) . "'
                            )");
                        }
                    }
                    
                }
            }
            
            $this->success = true;
            $this->successText = $added . " Schüler aktualisiert / eingefügt. " . $deleted . " gelöscht.";
        }
        else {
            $this->successText = 'Keine Schüler vom Mutterportal empfangen.';
        }

        
	}
	
	public function getName() {
		return "Schlülerdaten mit Mutterportal synchronisieren";
	}
	
	public function getDescription() {
		return "";
	}
	
	/**
	 *
	 *
	 * @return ['success' => 'true/false', 'resultText' => 'Text, der in der Administration angezeigt wird.']
	 */
	public function getCronResult() {
	   	return [
	       'success' => true,
	       'resultText' => $this->successText
	    ];
	}
	
	public function informAdminIfFail() {
		return false;
	}
	
	public function executeEveryXSeconds() {
	    return 86400;		// Einmal am Tag ausführen
	}
}



?>