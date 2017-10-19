<?php



class DB {
	private static $db;
	private static $tpl;
	private static $session = null;
	private static $settings = null;
	private static $globalsettings = null;

	public static $mySettings = array();

	public static function boo() {
	    self::$globalsettings = new GlobalSettings();
	}

	public static function start() {
		self::$globalsettings = new GlobalSettings();
		self::$db = new mysql();
		self::$tpl = new tpl();
		self::$db->connect();
		self::$settings = new settings();
		self::$settings->init();
	}

	public static function getDB() {
		return self::$db;
	}

	public static function getTPL() {
		return self::$tpl;
	}

	/**
	 * 
	 * @return settings
	 */
	public static function getSettings() {
		return self::$settings;
	}

	public static function initSession($sessionID) {
		$data = self::$db->query_first("SELECT * FROM sessions WHERE sessionID='".$sessionID."'");
		self::$session = new session($data);
	}

	public static function getSession() {
		return self::$session;
	}

	public static function isLoggedIn() {
		return (self::$session != null && self::$session->getData('userID') > 0 );
	}

	public static function getUserID() {
		if(self::$session != null && self::$session->getData("userID") > 0) return self::$session->getData("userID");
		else return 0;
	}

	public static function showError($message) {
		new errorPage($message);
	}

	/**
	 * 
	 * @return GlobalSettings
	 */
	public static function getGlobalSettings() {
		return self::$globalsettings;
	}

	public static function getVersion() {
		return '2.6';
	}

	/**
	 * Überprüft, ob das App Cookie gesetzt ist.
	 */
	public static function isApp() {
		return isset($_COOKIE['schuleinterndevicetype']);
	}

	/**
	 * Liest alle Netzwerke aus, die intern von SchuleIntern verwendet werden, und somit nicht für Synchronisationen zur Verfügung stehen.
	 * @return string[]
	 */
	public static function getInternalNetworks() {
		return array(
			"SCHULEINTERN",
			"SCHULEINTERN_SCHUELER",
			"SCHULEINTERN_LEHRER",
			"SCHULEINTERN_ELTERN"
		);
	}
	
	
	/**
	 * Überprüft, ob der DEBUG Modus an ist.
	 * @return boolean
	 */
	public static function isDebug() {
		return $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || substr($_SERVER['REMOTE_ADDR'],0,3) == '10.';
	}
	
	/**
	 * Liest den Stand der Daten aus der ASV aus.
	 * @return String natural Date des letzten Imports.
	 */
	public static function getAsvStand() {
		$stand = self::getSettings()->getValue("last-asv-import");
		if($stand != "") return $stand;
		return 'n/a';
	}
	
	/**
	 * Hat die Instanz eine Notenverwaltung?
	 * @return boolean
	 */
	public static function hasNotenverwaltung() {
		return DB::getGlobalSettings()->hasNotenverwaltung;
	}
}



?>