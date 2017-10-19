<?php



/**
 * Abstrakte Seite auf der alle andere Seiten aufbauen.
 * @author Spitschka IT Solutions
 * @version 1
 */
abstract class AbstractPage {
	private $title;

	public $header = "";

	public $footer = "";

	protected $sitename = "index";
	
	protected $loginStatus = "";
	protected $userImage = "";
	

	private static $activePages = array();
	
	public function __construct($pageline, $foo = false, $isAdmin = false) {
		$this->sitename = addslashes ( trim ( $_REQUEST ['page'] ) );
				
		if ($this->sitename != "" && in_array($this->sitename, requesthandler::getAllowedActions()) && !self::isActive ( $this->sitename )) {
			die ( "Die angegebene Seite ist leider nicht aktiviert" );
		}
		
			
			if (isset ( $_COOKIE ['ds2020session'] )) {
			    			    
				
				DB::initSession ( $_COOKIE ['ds2020session'] );
				
				
				
				if (! DB::isLoggedIn ()) {
					if (isset ( $_COOKIE ['ds2020session'] ))
						setcookie ( "ds2020session", null );
					
					$message = "<div class=\"callout callout-danger\"><p><strong>Sie waren leider zu lange inaktiv. Sie k&ouml;nnen dauerhaft angemeldet bleiben, wenn Sie den Haken bei \"Anmeldung speichern\" setzen. </strong></p></div>";
					
					eval ( "echo(\"" . DB::getTPL ()->get ( "login/index" ) . "\");" );
					
					exit ();
				} else {
					DB::getSession ()->update ();
				}
			}
			
			// Wartungsmodus
			
			$infoWartungsmodus = "";
			
			if (DB::getSettings ()->getValue ( "general-wartungsmodus" ) && $_REQUEST ['page'] != "login" && $_REQUEST ['page'] != "logout" && $_REQUEST ['page'] != "impressum") {
				if (! DB::isLoggedIn () || ! DB::getSession ()->isAdmin ()) {
					eval ( "echo(\"" . DB::getTPL ()->get ( "wartungsmodus/index" ) . "\");" );
					exit ();
				} else {
					$infoWartungsmodus = "<div class=\"callout callout-danger\"><i class=\"fa fa-cogs\"></i> Die Seite befindet sich im Wartungsmodus! Bitte unter den <a href=\"index.php?page=administrationmodule&module=index\">Einstellungen</a> wieder deaktivieren!</div>";
				}
			}
		
			
			// /Wartungsmodus
			
			// /Datenschutz
			
			$this->prepareHeaderBar ();
			
						
			$menu = new menu ($isAdmin);
			$menuHTML = $menu->getHTML ();
			
			$sitemapline = "";
			
			for($i = 0; $i < sizeof ( $pageline ); $i ++) {
				$sitemapline .= '<li class="active">' . $pageline [$i] . '</li>';
			}
			
			$siteTitle = $pageline [sizeof ( $pageline ) - 1];
			
			// Login Status
			
			if (DB::isLoggedIn ()) {
				$displayName = DB::getSession ()->getData ( 'userFirstName' ) . " " . DB::getSession ()->getData ( 'userLastName' );
				if (DB::isLoggedIn () && DB::getSession ()->isTeacher ())
					$mainGroup = "Lehrer";
				else if (DB::isLoggedIn () && DB::getSession ()->isPupil ())
					$mainGroup = "Schüler (Klasse " . DB::getSession ()->getPupilObject ()->getGrade () . ")";
				else if (DB::isLoggedIn () && DB::getSession ()->isEltern ())
					$mainGroup = "Eltern";
				else
					$mainGroup = "Sonstiger Benutzer";
			} else {
				$displayName = "Nicht angemeldet";
				$mainGroup = "";
			}
			
			$skinColor = 'green';			
			
			eval ( "\$this->header =  \"" . DB::getTPL ()->get ( 'header/header' ) . "\";" );
			eval ( "\$this->footer =  \"" . DB::getTPL ()->get ( 'footer' ) . "\";" );
	}

	private function prepareHeaderBar() {
		if(DB::isLoggedIn()) {

			$this->taskItem = "";

			$displayName = DB::getSession()->getData('userFirstName') . " " . DB::getSession()->getData('userLastName');
			
			if(DB::isLoggedIn() && DB::getSession()->isTeacher()) $mainGroup = "Lehrer";
			else if(DB::isLoggedIn() && DB::getSession()->isPupil()) $mainGroup = "Schüler (Klasse " . DB::getSession()->getPupilObject()->getGrade() . ")";
			else if(DB::isLoggedIn() && DB::getSession()->isEltern()) $mainGroup = "Eltern";
			else $mainGroup = "Anderer Benutzer";
			
			$this->userImage = "images/userimages/default.png";
			

			eval("\$this->loginStatus = \"" . DB::getTPL()->get("header/loginStatusLoggedIn") . "\";");
		}
		else {
			$this->displayName = "Nicht angemeldet";

			eval("\$this->loginStatus = \"" . DB::getTPL()->get("header/loginStatusNotLoggedIn") . "\";");
		}
	}


	/**
	 * Prüft, ob eine Person angemeldet ist.
	 */
	protected function checkLogin() {
		// Prüft, ob eine Person angemeldet ist.

	    
	    
		if(!DB::isLoggedIn()) {
			$page = $_REQUEST['page'];

			if(in_array($page, requesthandler::getAllowedActions())) {
				$redirectPage = $page;
			}
			else {
				$redirectPage = "index";
			}

			if($_REQUEST['message'] != "") {
				$message = "<div class=\"callout\">
         			<p><strong>" . addslashes($_REQUEST['message']) . "</strong></p>
        		</div>";
			}

			$valueusername = "";

			eval("echo(\"".DB::getTPL()->get("login/index")."\");");
			exit(0);
		}
		
		
	}

	/**
	 * Zeigt die Seite an.
	 */
	public abstract function execute();

	/**
	 * Überprüft, ob der angegebene Klassenname aktiviert ist.
	 * @param String $name Klassenname
	 * @return boolean
	 */
	public static function isActive($name) {

		if(sizeof(self::$activePages) == 0) {
			$pages = DB::getDB()->query("SELECT * FROM site_activation WHERE siteIsActive=1");

			while($p = DB::getDB()->fetch_array($pages)) {
				self::$activePages[] = $p['siteName'];
			}
		}


		if($name::siteIsAlwaysActive()) return true;

		return in_array($name, self::$activePages);

	}


	public static function hasSettings() {
		return false;
	}

	public static function getSettingsDescription() {
		return [];
	}

	/**
	 * Liest den Displaynamen der Seite aus.
	 */
	public abstract static function getSiteDisplayName();

	/**
	 * Zeigt an, ob die Seite immer aktiviert sein muss.
	 * @return boolean true: Seite kann nicht deaktiviert werden.
	 */
	public static function siteIsAlwaysActive() {
		return false;
	}
	
	/**
	 * Gibt an, ob eine Seite von anderen abhängig ist. Dadurch können diese nicht deaktiviert werden solange abgeleitete Seiten aktiv sind.
	 * @return String[] Seitennamen
	 */
	public static function dependsPage() {
		return [];
	}

	/**
	 * Überprüft, ob die Seite eine Administration hat.
	 * @return boolean
	 */
	public static function hasAdmin() {
		return false;
	}
	
	/**
	 * Icon im Menü
	 * @return string
	 */
	public static function getAdminMenuIcon() {
		return 'fa fa-cogs';
	}
	
	/**
	 * Menügruppe in der das Adminmodul angezeigt wird.
	 * @return string
	 */
	public static function getAdminMenuGroup() {
		return 'NULL';
	}
	
	/**
	 * Icon der Menügruppe
	 * @return string
	 */
	public static function getAdminMenuGroupIcon() {
		return 'fa fa-cogs';	// Zahnrad
	}
	
	/**
	 * Überprüft, ob die Seite eine Benutzeradministration hat.
	 * @return boolean
	 */
	public static function hasUserAdmin() {
		return false;
	}
	
	/**
	 * Liest die Gruppe aus, die Zugriff auf die Administration des Moduls hat.
	 * @return String Gruppenname als String
	 */
	public static function getAdminGroup() {
		return NULL;
	}
	
	/**
	 * Zeigt die Administration an. (Nur Bereich innerhalb des Main Body)
	 * @param $selfURL URL zu sich selbst zurück (weitere Parameter können vom Script per & angehängt werden.)
	 * @return HTML
	 */
	public static function displayAdministration($selfURL) {
		return "";
	}
	
	/**
	 * Räumt das Modul regelmäßig per Cron auf.
	 * @return Erfolgsmeldung
	 */
	public static function cronTidyUp() {
		return true;
	}
	
	/**
	 * 
	 * @param user $user Benutzer
	 * @return boolean Zugriff
	 */
	public static function userHasAccess($user) {
		return false;
	}
	
	
	/**
	 * Gibt an, welche Aktion beim Schuljahreswechsel durchgeführt wird. (Leer, wenn keine Aktion erfolgt.)
	 * @return String
	 */
	public static function getActionSchuljahreswechsel() {
		return '';
	}
	
	/**
	 * Führt den Schuljahreswechsel durch.
	 * @param String $sqlDateFirstSchoolDay Erster Schultag
	 */
	public static function doSchuljahreswechsel($sqlDateFirstSchoolDay) {
		
	}

}
