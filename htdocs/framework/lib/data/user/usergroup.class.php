<?php

class usergroup {

  private static $allGroups = [];

  private $name;

  private $members = [];

  private function __construct($name) {
    $this->name = $name;
    self::$allGroups[] = $this;
  }
  
  public function getName() {
  	return $this->name;
  }
  
  public function addUser($userID) {
  	DB::getDB()->query("INSERT INTO users_groups (userID, groupName) values('" . DB::getDB()->escapeString($userID) . "','" . $this->name . "') ON DUPLICATE KEY UPDATE userID=userID");
  }
  
  public function removeUser($userID) {
  	DB::getDB()->query("DELETE FROM users_groups WHERE userID='" . DB::getDB()->escapeString($userID) . "' AND groupName='" . $this->name . "'");
  }

  /**
   * @return user[]
   */
  public function getMembers() {
    if(sizeof($this->members) == 0) {
    	$users = DB::getDB()->query("SELECT * FROM users NATURAL JOIN users_groups WHERE groupName='" . $this->name . "' ORDER BY userName ASC, userLastName ASC, userFirstName ASC");
    	while($u = DB::getDB()->fetch_array($users)) {
    		$this->members[] = new user($u);
    	}
    }
    
    return $this->members;
  }

  public static function getAllByUserID($userID) {

    $groupAnswer = [];

    $groups = DB::getDB()->query("SELECT * FROM users_groups WHERE userID='" . $userID . "'");

    while($g = DB::getDB()->fetch_array($groups)) {
      $found = false;
      for($i = 0; $i < sizeof(self::$allGroups); $i++) {
        if(self::$allGroups[$i]->name == $g['groupName']) {
          $groupAnswer[] = self::$allGroups[$i];
          $found = true;
        }
      }

      if(!$found) {
        $groupAnswer[] = new usergroup($g['groupName']);
      }
    }

  }

  public static function getGroupByName($group) {
    for($i = 0; $i < sizeof(self::$allGroups); $i++) {
      if(self::$allGroups[$i]->name == $group) {
        return self::$allGroups[$i];
      }
    }

    return new usergroup($group);
  }
}

