<?php

class index extends AbstractPage {

  public function __construct() {
            
      
    parent::__construct ( array (
      "Digitale Schule 2020"
    ) );
    
    

    $this->checkLogin();

  }

  public function execute() {
    echo($this->header);
    
    echo($this->footer);

    exit();
  }

  public static function getSettingsDescription() {
    $settings = array();

    $settings[] = array(
    	'name' => 'general-wartungsmodus',
    	'typ' => 'BOOLEAN',
    	'titel' => "Wartungsmodus aktiv?",
    	'text' => 'Im Wartungsmodus können nur Administratoren die Seite nutzen. Es können sich nur Administratoren einloggen. Alle anderen sehen einen Hinweis auf die Wartungsarbeiten.'
    );


    /*$settings[] = array(
        'name' => 'general-homepage',
        'typ' => 'ZEILE',
        'titel' => "Homepage",
        'text' => 'Link zur Homepage der Schule. (Mit http:// oder https://)'
    );*/

    return $settings;
  }

  public static function getSiteDisplayName() {
    return "Schuljahr / Wartungsmodus";
  }

  public static function hasSettings() {
    return true;
  }

  public static function getUserGroups() {
    return array();

  }

  public static function siteIsAlwaysActive() {
    return true;
  }

  public static function hasAdmin() {
  	return true;
  }

  public static function getAdminGroup() {
  	return 'Webportal_Admin_General_Settings';
  }

  public static function getAdminMenuGroup() {
  	return 'Allgemeine Einstellungen';
  }

  public static function displayAdministration($selfURL) {
  	/**
  	 *
    $settings[] = array(
        'name' => 'general-schuljahr',
        'typ' => 'ZEILE',
        'titel' => "Aktuelles Schuljahr",
        'text' => 'Aktuelles Schuljahr im Format 2015/16'
    );
  	 */

  	if($_GET['action'] == 'doChange') {
  		$newDate = $_POST['ersterSchultag'];

  		if(!DateFunctions::isNaturalDate($newDate)) {
  			new errorPage("Ungültiges Datum");
  		}

  		$newDate = DateFunctions::getMySQLDateFromNaturalDate($newDate);

  		DB::getSettings()->setValue('general-schuljahr', $_REQUEST['neuesSchuljahr']);

  		$alleSeiten = requesthandler::getAllowedActions();

  		$htmlActions = '';

  		for($i = 0; $i < sizeof($alleSeiten); $i++) {
  			if($alleSeiten[$i]::getActionSchuljahreswechsel() != '' & AbstractPage::isActive($alleSeiten[$i])) {
  				$alleSeiten[$i]::doSchuljahreswechsel($newDate);
  			}
  		}

  		header("Location: $selfURL&success=1");
  		exit(0);
  	}

  	$alleSeiten = requesthandler::getAllowedActions();

  	$htmlActions = '';

  	for($i = 0; $i < sizeof($alleSeiten); $i++) {
  		if($alleSeiten[$i]::getActionSchuljahreswechsel() != '' & AbstractPage::isActive($alleSeiten[$i])) {
  			$htmlActions .= "<tr><td>" . $alleSeiten[$i]::getSiteDisplayName() . "</td><td>" . $alleSeiten[$i]::getActionSchuljahreswechsel() . "</td></tr>";
  		}
  	}

  	$html = '';

  	eval("\$html = \"" . DB::getTPL()->get("administration/schuljahreswechsel/index") ."\";");

  	return $html;
  }
}

?>
