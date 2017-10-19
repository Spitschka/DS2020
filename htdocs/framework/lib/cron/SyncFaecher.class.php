<?php

class SyncFaecher extends AbstractCron {

	private $success = false;
	private $successText = '';
	
	public function __construct() {
	}

	public function execute() {
        $faecher = PortalApi::getAllFaecher();        
        
        $added = 0;
        
        DB::getDB()->query("TRUNCATE faecher");
        DB::getDB()->query("TRUNCATE fachbetreuer");
               
        if(sizeof($faecher) > 0) {
      
            for($i = 0; $i < sizeof($faecher); $i++) {
                DB::getDB()->query("INSERT INTO faecher
                    (fachID, fachKurzform, fachLangform) values(
                        '" . DB::getDB()->escapeString($faecher[$i]->fachID) . "',
                        '" . DB::getDB()->escapeString($faecher[$i]->fachKurzform) . "',
                        '" . DB::getDB()->escapeString($faecher[$i]->fachLangform) . "'
                    )");
                
                
                for($f = 0; $f < sizeof($faecher[$i]->fachbetreuer); $f++) {
                    DB::getDB()->query("INSERT INTO fachbetreuer (fachID, lehrerAsvID) values('" . DB::getDB()->escapeString($faecher[$i]->fachID) . "','" . DB::getDB()->escapeString($faecher[$i]->fachbetreuer[$f]) . "')");
                }
                
                $added++;
            }
            
            $this->success = true;
            $this->successText = $added . " Fächer aktualisiert / eingefügt.";
        }
        else {
            $this->successText = 'Keine Fächer vom Mutterportal empfangen.';
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