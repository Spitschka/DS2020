<?php


class functions {
	private function __construct() {
	}
	
	private static $monthNames = array("","Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");

	public static function getFormatedDateFromSQLDate($date) {
		$date = explode("-", $date);
		
		return $date[2] . ". " . self::$monthNames[$date[1]*1] . " " . $date[0];
		
	}
	
	public static function isNumber($number) {
		$numbers = [
			"0",
			"1",
			"2",
			"3",
			"4",
			"5",
			"6",
			"7",
			"8",
			"9"
		];
		
		return in_array($number, $numbers);
	}
	
	public static function getFormatedDateWithDayFromSQLDate($date) {
		$date = explode("-", $date);
		
		$day = mktime(21,00,00,$date[1]*1,$date[2],$date[0]);
		
		return self::getDayName(date("N",$day)-1) . ", " .  $date[2] . ". " . self::$monthNames[$date[1]*1] . " " . $date[0];
	}
	
	public static function getDayName($day) {
		
		if($day < 0) $day = 6;
		
		switch($day) {
			case 0: return "Montag";
			case 1: return "Dienstag";
			case 2: return "Mittwoch";
			case 3: return "Donnerstag";
			case 4: return "Freitag";
			case 5: return "Samstag";
			case 6: return "Sonntag";
		}
		
		return "Unknown Day";
	}
	
	public static function makeDateFromTimestamp($time) {
		return date("d.m.Y - H:i",$time) . " Uhr";
	}
	
	public static function getDisplayNameFromUserID($userID) {
		$name = DB::getDB()->query_first("SELECT userName, userFirstName, userLastName FROM users WHERE userID='" . $userID . "'");
		
		if($name['userName'] != "") {
			$name = $name['userLastName'] . ", " . $name['userFirstName'] . " (" . $name['userName'] . ")";
		}
		
		return $name;
	}
	
	public static function getIntArrayFromTill($from,$till) {
		$t = array();
		for($i = $from; $i <= $till; $i++) $t[] = $i;
		return $t;
	}
	
	public static function isOneElementOfArrayInOtherArray($array, $other) {
		for($i = 0; $i < sizeof($array); $i++) {
			if(in_array($array[$i],$other)) return true;
		}
		
		return false;
	}
}

?>