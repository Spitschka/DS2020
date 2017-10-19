<?php

class requesthandler {
  private static $actions = [
    'absenzen' => [
      'absenzenberichte',
      'absenzenlehrer',
      'absenzensekretariat',
      'absenzenstatistik',
      'absenzenschueler'
    ],
    'administration' => [
      'administration',
      'administrationactivatepages',
      'administrationmodule',
      'administrationcron'
    ],
    'krankmeldung' => [
      'krankmeldung',
    ],
    'beurlaubung' => [
      'beurlaubungantrag'
    ],
    'respizienz' => [
        'respizienz'
    ],
    'system' => [
      'errorPage',
      'index'
    ],
    'loginlogout' => [
      'login',
      'logout'
    ],
    'files' => [
      'FileDownload'
    ],
    'test' => [
        'test'
    ]
  ];

  public function __construct($action) {
      
    $allowed = false;
    
    require_once ('../framework/lib/page/abstractPage.class.php');

    foreach(self::$actions as $f => $pages) {
      for($p = 0; $p < sizeof($pages); $p++) {
        include_once('../framework/lib/page/' . $f . '/' . $pages[$p] . '.class.php');
        if($pages[$p] == $action) $allowed = true;
      }
    }

    if($allowed) {
      try {
        /**
         * 
         * @var AbstractPage $page
         */
        $page = new $action;
        
        $page->execute();
      }
      catch(Throwable $e) {
        echo "<b>" . $e->getMessage() . "</b> in Line " . $e->getLine()  . " in " . $e->getFile() . "<br />";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
      }
    }
    else {
      new errorPage('Nicht erlaubt');
      die();
    }
  }

  public static function getAllowedActions() {
    $ps = [];
    foreach(self::$actions as $f => $pages) {
      for($p = 0; $p < sizeof($pages); $p++) {
        $ps[] = $pages[$p];
      }
    }
    
    return $ps;
  }

  

 

}

?>
