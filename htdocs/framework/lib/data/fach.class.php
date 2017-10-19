<?php

class fach {
  private static $all = [];

  private $data = [];

  private function __construct($data) {
    $this->data = $data;
  }

  public function getID() {
    return $this->data['fachID'];
  }

  public function getKurzform() {
    return $this->data['fachKurzform'];
  }


  public function getLangform() {
    return $this->data['fachLangform'];
  }

  /**
   *
   * @return fach[] alle
   */
  public static function getAll() {

    if(sizeof(self::$all) == 0) {
      $alleSQL = DB::getDB()->query("SELECT * FROM faecher ORDER BY fachLangform ASC");
      while($d = DB::getDB()->fetch_array($alleSQL)) self::$all[] = new fach($d);
    }

    return self::$all;
  }

  /**
   * 
   * @param lehrer $lehrer
   */
  public function isFachschaftsleitung($lehrer) {
      $fss = DB::getDB()->query("SELECT * FROM fachbetreuer WHERE fachID='" . $this->getID() . "'");
      while($fs = DB::getDB()->fetch_array($fss)) if($fs['lehrerAsvID'] == $lehrer->getAsvID()) return true;
      
      return false;
  }
  
  /**
   *
   * @param int $id
   * @return fach|null
   */
  public static function getByID($id) {
    $all = self::getAll();

    for($i = 0; $i < sizeof($all); $i++) {
      if($all[$i]->getID() == $id) return $all[$i];
    }

    return null;
  }

  public static function getDummy() {
    return new fach([
      'fachID' => 0,
      'fachKurzform' => 'n/a',
      'fachLangform' => 'n/a'
    ]);
  }
}
