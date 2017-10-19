<?php

class Leistungsnachweis {

    private static $terminAk = [
      'SCHULAUFGABE' => 'SA',
      'STEGREIFAUFGABE' => 'EX',
      'KURZARBEIT' => 'KA',
      'PLNW' => 'PLNW',
      'MODUSTEST' => 'MODUS',
      'NACHHOLSCHULAUFGABE' => 'SA (Nachtermin)'
    ];

    private static $terminLangnamen = [
      'SCHULAUFGABE' => 'Schulaufgabe',
      'STEGREIFAUFGABE' => 'Stegreifaufgabe',
      'KURZARBEIT' => 'Kurzarbeit',
      'PLNW' => 'Praktischer Leistungsnachweis',
      'MODUSTEST' => 'Modustest',
      'NACHHOLSCHULAUFGABE' => 'Nachholschulaufgabe'
    ];

    private static $terminFarben = [
    'SCHULAUFGABE' => 'blue',
    'STEGREIFAUFGABE' => 'red',
    'KURZARBEIT' => 'purple',
    'KLASSENTERMIN' => 'green',
    'MODUSTEST' => 'MediumPurple',
    'PLNW' => 'red',
    'NACHHOLSCHULAUFGABE' => 'blue'
    ];

    private $data;

    public function __construct($id,$dataLine) {
      $this->data = [
          'eintragID' => $id,
          'eintragArt' => $dataLine->art,
          'eintragKlasse' => $dataLine->klasse,
          'eintragDatumStart' => $dataLine->datum,
          'eintragLehrer' => $dataLine->lehrer,
          'eintragFach' => $dataLine->fach,
          'eintragIsAngekuendigt' => $dataLine->isAngekuendigt > 0
      ]; 
    }
    
    /**
     * Liest die ID des Eintrags aus.
     * @return int ID des Eintrags
     */
    public function getID() {
    	return $this->data['eintragID'];
    }

    public function getArt() {
      return $this->data['eintragArt'];
    }

    public function getArtKurztext() {
      return self::$terminAk[$this->data['eintragArt']];
    }

    public function getArtLangtext() {
      return self::$terminLangnamen[$this->data['eintragArt']];
    }

    public function getEintragFarbe() {
      return self::$terminFarben[$this->data['eintragArt']];
    }

    public function getKlasse() {
      return $this->data['eintragKlasse'];
    }

    public function getDatumStart() {
      return $this->data['eintragDatumStart'];
    }

    public function getLehrer() {
      return $this->data['eintragLehrer'];
    }

    public function getFach() {
      return $this->data['eintragFach'];
    }
 
    public function showForNotTeacher() {
        return $this->data['eintragIsAngekuendigt'];
    }
    
    public function isAlwaysShow() {
        return $this->data['eintragIsAngekuendigt'];
    }
}
