<?php


class respizienz extends AbstractPage {
	
	private $isSchulleitung = false;
	
	
	
	public function __construct() {
		
		parent::__construct ( array (
			"Respizienz" 
		) );
		
		$this->checkLogin();
		
		$accessOK = false;
		
		if(DB::getSession()->isTeacher()) {
		    $this->isSchulleitung = DB::getSession()->getTeacherObject()->isSchulleitung();
		    $accessOK = true;
		}
		
		if(DB::getSession()->getUser()->isSekretariat()) {
		    $this->isSchulleitung = true;
		    $accessOK = true;
		}
		
		
		if(!$accessOK) {
			new errorPage();
		}
	}
	
	public function execute() {		
        switch($_REQUEST['mode']) {
            case 'schulleitung':
                $lnws = LeistungsnachweisRespizienz::getBySchulleitung(DB::getSession()->getTeacherObject());
                $this->respizienzDialog($lnws, true, false);
                break;
            
            case 'fachbetreuer':
                $lnws = LeistungsnachweisRespizienz::getByFachbetreuer(DB::getSession()->getTeacherObject());
                $this->respizienzDialog($lnws, false, true);
                
            default:
                $lnws = LeistungsnachweisRespizienz::getbyTeacher(DB::getSession()->getTeacherObject());
                $this->respizienzDialog($lnws, false, false);
            break;
        }
	}
	
	/**
	 * 
	 * @param LeistungsnachweisRespizienz[] $meine
	 */
	private function respizienzDialog($meine, $isSchulleitung, $isFachbetreuer) {
	    // $meine = LeistungsnachweisRespizienz::getByFachbetreuer(DB::getSession()->getTeacherObject());
	    
	    
	    if($isSchulleitung) $mode = "&mode=schulleitung";
	    if($isFachbetreuer) $mode = "&mode=fachbetreuer";
	    
	    
	    $meineHTML = '';
	    $dialoge = '';
	    for($i = 0; $i < sizeof($meine); $i++) {
	        
	        if(!$isFachbetreuer && !$isSchulleitung) {
	        
    	        if($_REQUEST['action'] == 'setAnalog' && $_REQUEST['respizienzID'] == $meine[$i]->getID()) {
    	            $meine[$i]->setAnalog(true);
    	        }
    	        
    	        if($_REQUEST['action'] == 'setDigital' && $_REQUEST['respizienzID'] == $meine[$i]->getID()) {
    	            $meine[$i]->setAnalog(false);
    	        }
	        
	        }
	        
	        
	        if($_REQUEST['action'] == 'uploadFile' && $_REQUEST['respizienzID'] == $meine[$i]->getID()) {
	            $upload = FileUpload::uploadPDF('pdfFile', $meine[$i]->getLangname() . " - " . $meine[$i]->getKlasse() . " - " . $meine[$i]->getDatumAsNaturalDate());
	            
	            
	            if($upload['result'] > 0) {
	                if($isSchulleitung) {
	                    $meine[$i]->setSLFile($upload['uploadobject']->getID());
	                    $meine[$i]->setSLLehrer(DB::getSession()->getTeacherObject());
	                }
	                else if($isFachbetreuer) {
	                    $meine[$i]->setFSLFile($upload['uploadobject']->getID());
	                    $meine[$i]->setFSLLehrer(DB::getSession()->getTeacherObject());
	                }
	                else $meine[$i]->setFile($upload['uploadobject']->getID());
	            }
	            
	        }
	        
	        if($_REQUEST['action'] == 'deleteFile' && $_REQUEST['respizienzID'] == $meine[$i]->getID()) {
	            $uploadFile = null;
	            
	            if($isSchulleitung) {
	                $uploadFile = $meine[$i]->getSLFile();
	                $meine[$i]->setSLFile(0);
	            }
	            else if($isFachbetreuer) {
	                $uploadFile = $meine[$i]->getFSLFile();
	                $meine[$i]->setFSLFile(0);
	            }
	            else {
	                $uploadFile = $meine[$i]->getFile();
	                $meine[$i]->setFile(0);
	            }
	            
	            
	            if($uploadFile != null) {
	                $uploadFile->delete();
	            }
	            
	            
	            
	            
	        }
	        
	        if($meine[$i]->getFile() != null) {
	            $file = $meine[$i]->getFile();
	            $downloadFile = "<a href=\"" . $file->getURLToFile() . "\"><i class=\"fa fa-file-pdf-o\"></i> Download</a>";
	            if(!$isSchulleitung && !$isFachbetreuer) {
	                $downloadFile .= "<br /><button type=\"button\" class=\"btn btn-sm\" onclick=\"uploadFile(" . $meine[$i]->getID() . ");\"><i class=\"fa fa-upload\"></i> PDF neu hochladen</button>";
	                $downloadFile .= " <button type=\"button\" onclick=\"javascript:confirmAction('Soll die hinterlegte PDF Datei wirklich gelöscht werden?','index.php?page=respizienz&action=deleteFile&respizienzID={$meine[$i]->getID()}{$mode}');\" class=\"btn btn-sm btn-danger\"><i class=\"fa fa-trash\"></i></button>";
	                
	            }
	            
	        }
	        else {
	            $downloadFile = '<i>Bisher keine Datei</i>';
	            if(!$isSchulleitung && !$isFachbetreuer) {
	               $downloadFile .= "<br /><button type=\"button\" class=\"btn btn-sm\" onclick=\"uploadFile(" . $meine[$i]->getID() . ");\"><i class=\"fa fa-upload\"></i> PDF hochladen</button>";
	            }
	        }
	        
	        
	        if($meine[$i]->getFSLFile() != null) {
	            $fileFSL = $meine[$i]->getFSLFile();
	            $downloadFileFSL = "<a href=\"" . $fileFSL->getURLToFile() . "\"><i class=\"fa fa-file-pdf-o\"></i> Download</a>";
	            
	            $lehrer = $meine[$i]->getFSLLehrer();
	            if($lehrer != null) $downloadFileFSL .= "<br />Repiziert von " . $lehrer->getDisplayNameMitAmtsbezeichnung();
	        
	            if($isFachbetreuer) {
	                $downloadFileFSL .= "<br /><button type=\"button\" class=\"btn btn-sm\" onclick=\"uploadFile(" . $meine[$i]->getID() . ");\"><i class=\"fa fa-upload\"></i> PDF neu hochladen</button>";
	                $downloadFileFSL .= " <button type=\"button\" onclick=\"javascript:confirmAction('Soll die hinterlegte PDF Datei wirklich gelöscht werden?','index.php?page=respizienz&action=deleteFile&respizienzID={$meine[$i]->getID()}{$mode}');\" class=\"btn btn-sm btn-danger\"><i class=\"fa fa-trash\"></i></button>";
	            }
	        }
	        else {
	            $downloadFileFSL = '<i>Bisher keine Datei</i>';
	            
	            if($isFachbetreuer) {
	                $downloadFileFSL .= "<br /><button type=\"button\" class=\"btn btn-sm\" onclick=\"uploadFile(" . $meine[$i]->getID() . ");\"><i class=\"fa fa-upload\"></i> PDF hochladen</button>";
	            }
	        }
	        
	        if($meine[$i]->getSLFile() != null) {
	            $fileSL = $meine[$i]->getSLFile();
	            $downloadFileSL = "<a href=\"" . $fileSL->getURLToFile() . "\"><i class=\"fa fa-file-pdf-o\"></i> Download</a>";
	            
	            $lehrer = $meine[$i]->getSLLehrer();
	            if($lehrer != null) $downloadFileSL .= "<br />Repiziert von " . $lehrer->getDisplayNameMitAmtsbezeichnung();
	            
	            if($isSchulleitung) {
	                $downloadFileSL .= "<br /><button type=\"button\" class=\"btn btn-sm\" onclick=\"uploadFile(" . $meine[$i]->getID() . ");\"><i class=\"fa fa-upload\"></i> PDF neu hochladen</button>";
	                $downloadFileSL .= " <button type=\"button\" onclick=\"javascript:confirmAction('Soll die hinterlegte PDF Datei wirklich gelöscht werden?','index.php?page=respizienz&action=deleteFile&respizienzID={$meine[$i]->getID()}{$mode}');\" class=\"btn btn-sm btn-danger\"><i class=\"fa fa-trash\"></i></button>";
	            }
	        }
	        else {
	            $downloadFileSL = '<i>Bisher keine Datei</i>';
	            if($isSchulleitung) {
	                $downloadFileSL .= "<br /><button type=\"button\" class=\"btn btn-sm\" onclick=\"uploadFile(" . $meine[$i]->getID() . ");\"><i class=\"fa fa-upload\"></i> PDF hochladen</button>";
	            }
	        }
	        
	        $notenHTML = "";
	        
	        $noten = $meine[$i]->getSchuelerMitNoten();
	        
	        for($n = 0; $n < sizeof($noten); $n++) {
	            $notenHTML .= "<tr><td>" . $noten[$n]['schueler']->getCompleteSchuelerName() . "</td><td>" . $noten[$n]['note'] . "</td></tr>";
	        }
	        
	        
	        eval("\$dialoge .= \"" . DB::getTPL()->get("respizienz/notenDialog") . "\";");
	        eval("\$meineHTML .= \"" . DB::getTPL()->get("respizienz/bit") . "\";");
	    }

	    
	    
	    eval("DB::getTPL()->out(\"" . DB::getTPL()->get('respizienz/index') . "\");");
	    
	}
	
	public static function hasSettings() {
		return false;
	}
	
	public static function getSettingsDescription() {
		return [];
	}
	
	
	public static function getSiteDisplayName() {
		return 'Online Respizienz';
	}
	
	/**
	 * Liest alle Nutzergruppen aus, die diese Seite verwendet. (Für die Benutzeradministration)
	 * @return array(array('groupName' => '', 'beschreibung' => ''))
	 */
	public static function getUserGroups() {
		return array();
	}
	
	public static function hasAdmin() {
		return true;
	}

	public static function dependsPage() {
		return [];
	}
	
	public static function userHasAccess($user) {
	    
	    if(DB::getSession()->isTeacher()) {
	        return true;
	    }
	
		if(DB::getSession()->getUser()->isSekretariat()) return true;
	    
		return false;
	}
	
}

?>