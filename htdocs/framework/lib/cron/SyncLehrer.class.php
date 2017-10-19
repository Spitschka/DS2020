<?php

class SyncLehrer extends AbstractCron {

	private $success = false;
	private $successText = '';
	
	public function __construct() {
	}

	public function execute() {
        $lehrer = PortalApi::getAllLehrer();
                
        $added = 0;
        
        if(sizeof($lehrer) > 0) {
            DB::getDB()->query("TRUNCATE lehrer");
            DB::getDB()->query("TRUNCATE klassenleitung");
            
            for($i = 0; $i < sizeof($lehrer); $i++) {
                DB::getDB()->query("INSERT INTO lehrer
                    (
                        lehrerAsvID,
                        lehrerKuerzel,
                        lehrerName,
                        lehrerRufname,
                        lehrerGeschlecht,
                        lehrerAmtsbezeichnung,
                        lehrerIsSchulleitung
                    ) values
                    (
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerAsvID) . "',
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerKuerzel) . "',
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerName) . "',
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerRufname) . "',
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerGeschlecht) . "',
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerAmtsbezeichnung) . "',
                        '" . (DB::getDB()->escapeString($lehrer[$i]->lehrerIsSchulleitung) > 0) . "'                       
                    )
                ");
                
                $lehrerID = DB::getDB()->insert_id();
                
                for($k = 0; $k < sizeof($lehrer[$i]->lehrerKlassenleitung); $k++) {
                    DB::getDB()->query("INSERT INTO klassenleitung (klasseName, lehrerID, klassenleitungArt) values(
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerKlassenleitung[$k]->klasse) . "',
                        '" . $lehrerID . "',
                        '" . DB::getDB()->escapeString($lehrer[$i]->lehrerKlassenleitung[$k]->nummer) . "'
                    )
                    ");
                }
                
                $added++;
            }

            $this->success = true;
            $this->successText = $added . ' Lehrer eingefügt.';
        }
        else {
            $this->successText = 'Keine Klassenleitungen vom Mutterportal empfangen.';
        }

        
	}
	
	public function getName() {
		return "Lehrerdaten, Klassenltungen mit Mutterportal synchronisieren";
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