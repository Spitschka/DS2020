<?php


class LeistungsnachweisRespizienz {
    
    private static $langnamen = [
        'SCHULAUFGABE' => 'Schulaufgabe',
        'STEGREIFAUFGABE' => 'Stegreifaufgabe',
        'KURZARBEIT' => 'Kurzarbeit',
        'PLNW' => 'Praktischer Leistungsnachweis',
        'MODUSTEST' => 'Modustest',
        'NACHHOLSCHULAUFGABE' => 'Nachholschulaufgabe'
    ];
    
    
    private $data;
    private $portaldata;
    
    private function __construct($data, $portaldata) {
        $this->data = $data;
        $this->portaldata = $portaldata;
    }
    
    public function getID() {
        return $this->data['respizienzID'];
    }
    
    public function getFach() {
        return fach::getByID($this->portaldata->fachID);
    }
    
    public function getLehrer() {
        return lehrer::getByASVId($this->portaldata->lehrerAsvID);
    }
    
    public function isFSLRespizieiert() {
        return $this->data['respizientFSLFile'] > 0;
    }
    
    public function getFSLLehrer() {
        return lehrer::getByASVId($this->data['respizientFSLLehrer']);
    }
    
    public function isSLRespizieiert() {
        return $this->data['respizientFSLFile'] > 0;
    }
    
    public function getSLLehrer() {
        return lehrer::getByASVId($this->data['respizientSLLehrer']);
    }
    
    public function getFile() {
        return FileUpload::getByID($this->data['respizienzFile']);
    }
    
    public function getFSLFile() {
        return FileUpload::getByID($this->data['respizientFSLFile']);
    }
    
    public function getSLFile() {
        return FileUpload::getByID($this->data['respizientSLFile']);
    }
    
    public function setFile($file) {
        $this->data['respizienzFile'] = $file;
        $this->setVal('respizienzFile', $file);
    }
    
    public function setFSLFile($file) {
        $this->data['respizientFSLFile'] = $file;
        $this->setVal('respizientFSLFile', $file);
    }
    
    public function setSLFile($file) {
        $this->data['respizientSLFile'] = $file;
        $this->setVal('respizientSLFile', $file);
    }
    
    public function setFSLLehrer($lehrer) {
        $this->setVal('respizientFSLLehrer', $lehrer->getAsvID());
    }
    
    public function setSLLehrer($lehrer) {
        $this->setVal('respizientSLLehrer', $lehrer->getAsvID());
    }
    
    public function getLangname() {
        return self::$langnamen[$this->portaldata->art];
    }
    
    public function isAnalog() {
        return $this->data['respizienzIsAnalog'] > 0;
    }
    
    public function setAnalog($status) {
        $this->data['respizienzIsAnalog'] = $status ? 1 : 0;
        $this->setVal('respizienzIsAnalog', $status);
    }
    
    public function getKlasse() {
        return $this->portaldata->klasse;
    }
    
    public function getDatumAsNaturalDate() {
        return DateFunctions::getNaturalDateFromMySQLDate($this->portaldata->datum);
    }
    
    public function getSchuelerMitNoten() {
        $alleNoten = [];
        
        for($i = 0; $i < sizeof($this->portaldata->noten); $i++) {
            $alleNoten[] = [
                'schueler' => schueler::getByAsvID($this->portaldata->noten[$i]->schuelerAsvID),
                'note' => $this->portaldata->noten[$i]->note
            ];
        }
        
        return $alleNoten;
    }
    
    private function setVal($field, $val) {
        DB::getDB()->query("UPDATE respizienz SET $field = '" . DB::getDB()->escapeString($val) . "' WHERE respizienzID='" . $this->getID() . "'");
    }
    
    /**
     * 
     * @param lehrer $teacher
     */
    public static function getbyTeacher($teacher) {
        $portaldata = PortalApi::getRespizienzLeistungsnachweise($teacher->getAsvID());
                
        $alle = [];
        
        for($i = 0; $i < sizeof($portaldata); $i++) {
            $data = DB::getDB()->query_first("SELECT * FROM respizienz WHERE respizienzID='" . $portaldata[$i]->leistungsnachweisID . "'");
            if($data['respizienzID'] > 0) {
                
            }
            else {
                DB::getDB()->query("INSERT INTO respizienz (respizienzID) values('" . $portaldata[$i]->leistungsnachweisID . "')");
                $data = DB::getDB()->query_first("SELECT * FROM respizienz WHERE respizienzID='" . $portaldata[$i]->leistungsnachweisID . "'");
            }
            $alle[] = new LeistungsnachweisRespizienz($data, $portaldata[$i]);
        }
        
        return $alle;
    }
    
    /**
     *
     * @param lehrer $teacher
     */
    public static function getByFachbetreuer($teacher) {
        $portaldata = PortalApi::getRespizienzLeistungsnachweiseFachbetreuer($teacher->getAsvID());
        
        $alle = [];
        
        for($i = 0; $i < sizeof($portaldata); $i++) {
            $data = DB::getDB()->query_first("SELECT * FROM respizienz WHERE respizienzID='" . $portaldata[$i]->leistungsnachweisID . "'");
            if($data['respizienzID'] > 0) {
                
            }
            else {
                DB::getDB()->query("INSERT INTO respizienz (respizienzID) values('" . $portaldata[$i]->leistungsnachweisID . "')");
                $data = DB::getDB()->query_first("SELECT * FROM respizienz WHERE respizienzID='" . $portaldata[$i]->leistungsnachweisID . "'");
            }
            $alle[] = new LeistungsnachweisRespizienz($data, $portaldata[$i]);
        }
        
        return $alle;
    }
    
    /**
     *
     * @param lehrer $teacher
     */
    public static function getBySchulleitung($teacher) {
        $portaldata = PortalApi::getRespizienzLeistungsnachweiseSchulleitung($teacher->getAsvID());
        
        $alle = [];
        
        for($i = 0; $i < sizeof($portaldata); $i++) {
            $data = DB::getDB()->query_first("SELECT * FROM respizienz WHERE respizienzID='" . $portaldata[$i]->leistungsnachweisID . "'");
            if($data['respizienzID'] > 0) {
                
            }
            else {
                DB::getDB()->query("INSERT INTO respizienz (respizienzID) values('" . $portaldata[$i]->leistungsnachweisID . "')");
                $data = DB::getDB()->query_first("SELECT * FROM respizienz WHERE respizienzID='" . $portaldata[$i]->leistungsnachweisID . "'");
            }
            $alle[] = new LeistungsnachweisRespizienz($data, $portaldata[$i]);
        }
        
        return $alle;
    }
    
    
}