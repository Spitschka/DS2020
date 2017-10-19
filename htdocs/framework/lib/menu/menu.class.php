<?php


/**
 * Menü der Seite
 * @author Christian
 *
 */
class menu {
  private $html = "";

  public function __construct($isAdmin = false) {
    if($isAdmin) $this->adminMenu();
    else $this->normalMenu();
  }
  
  private function adminMenu() {
  	
  	if(!DB::isLoggedIn()) return;


    $modulAdminHTML = "";
    
    
    $modulAdminHTML .= "<li><div class=\"callout callout-danger\"><a href=\"index.php\"><i class=\"fa fa-arrow-left\"></i><span> Administration verlassen</span></div></li>";

    if(DB::getSession()->isAdmin()) $modulAdminHTML .= $this->getMenuItem('administrationactivatepages', 'Module de-/aktivieren', 'fa fa-check');


    $displayActions = [];

    /**
     *
     * @var AbstractPage[] $actions
     */
    $actions = requesthandler::getAllowedActions();
    for($i = 0; $i < sizeof($actions); $i++) {
      if($actions[$i]::hasAdmin()) {
        $view = true;

        if($view && (DB::getSession()->isAdmin() || in_array($actions[$i]::getAdminGroup(), DB::getSession()->getGroupNames()))) {
          if(!is_array($displayActions[$actions[$i]::getAdminMenuGroup()])) {
            $displayActions[$actions[$i]::getAdminMenuGroup()] = [
              $actions[$i]
            ];
          }
          else {
            $displayActions[$actions[$i]::getAdminMenuGroup()][] = $actions[$i];
          }
        }
      }
    }


    $unsorted = [];
    $sorted = [];

    foreach($displayActions as $kg => $pages) {
      $unsorted[] = $kg;
    }

    sort($unsorted);

    for($i = 0; $i < sizeof($unsorted); $i++) {
      $sorted[$unsorted[$i]] = $displayActions[$unsorted[$i]];
    }

    $displayActions = $sorted;




    foreach($displayActions as $kg => $pages) {

      sort($pages);

      if($kg != 'NULL') {
        $modulAdminHTML .= $this->startDropDown(['administrationmodule'], $kg, $pages[0]::getAdminMenuGroupIcon(), ['module' => $pages]);
      }


      for($i = 0; $i < sizeof($pages); $i++) {
        $modulAdminHTML .= $this->getMenuItem('administrationmodule', $pages[$i]::getSiteDisplayName(), $pages[$i]::getAdminMenuIcon(), ['module' => $pages[$i]]);
      }

      if($kg != 'NULL') {
        $modulAdminHTML .= $this->endDropDown();
      }

    }


  $this->html .= $modulAdminHTML;


  }

  private function normalMenu() {



    if(!DB::isLoggedIn()) return;

    
    $this->html .= $this->getMenuItem("index", "Start", "fa fa-home");

    $absenzen = "";

    if($this->isActive("absenzensekretariat") && absenzensekretariat::userHasAccess(DB::getSession()->getUser())) {

      $pages = ['absenzensekretariat', 'absenzenberichte','absenzenstatistik'];
      if(!DB::getSession()->isTeacher()) $pages[] = "absenzenlehrer";

      $absenzen .= $this->startDropDown($pages, "Absenzen Sekretariat", "fa fa-bed");
      $absenzen .= $this->getMenuItem("absenzensekretariat", "Hauptansicht", "fa fa-bed",['mode' => '']);
      $absenzen .= $this->getMenuItem("absenzenberichte", "Berichte", "fa fa-print");
      $absenzen .= $this->getMenuItem("absenzenstatistik", "Statistik", "fa fa-pie-chart");
      $absenzen .= $this->getMenuItem("absenzensekretariat", "Sammelbeurlaubung", "fa fa-bed",['mode' => 'sammelbeurlaubung']);
      $absenzen .= $this->getMenuItem("absenzensekretariat", "Periodische Beurlaubung", "fa fa-bed",['mode' => 'periodischeBeurlaubung']);
      $absenzen .= $this->getMenuItem("absenzensekretariat", "fpA Zeiten", "fa fa-wrench",['mode' => 'klassenanwesenheit']);
      // if(!DB::getSession()->isTeacher()) $absenzen .= $this->getMenuItem("absenzenlehrer", "Entschuldigungen überprüfen", "fa fa-check");
      $absenzen .= $this->endDropDown();
    }


    if($this->isActive("absenzenlehrer") && absenzenlehrer::userHasAccess(DB::getSession()->getUser())) {
      $absenzen .= $this->startDropDown(['absenzenlehrer'], "Absenzen Lehrer", "fa fa-bed");

      if(DB::getSession()->isTeacher() || DB::getSession()->isAdmin() || DB::getSession()->getUser()->isSekretariat()) $absenzen .= $this->getMenuItem("absenzenlehrer", "Entschuldigungen überprüfen", "fa fa-check", ['mode' => '']);
      $absenzen .= $this->getMenuItem("absenzenlehrer", "Alle Absenzen einsehen", "fa fa-eye", ['mode' => 'showTotal']);

      $absenzen .= $this->endDropDown();
    }


    if($this->isActive("krankmeldung") && krankmeldung::userHasAccess(DB::getSession()->getUser())) {
      $absenzen .= $this->getMenuItem("krankmeldung", "Krankmeldung", "fa fa-bed");
    }
    
    if($this->isActive("beurlaubungantrag") && beurlaubungantrag::userHasAccess(DB::getSession()->getUser())) {
        
        $absenzen .= $this->getMenuItem("beurlaubungantrag", "Beurlaubungsantrag", "fa fa-briefcase");
        
    }
    
    if($this->isActive("respizienz") && respizienz::userHasAccess(DB::getSession()->getUser())) {
        
        $absenzen .= $this->getMenuItem("respizienz", "Digitale Respizienz", "fa fa-briefcase", ['mode' => '']);
        if(DB::getSession()->isTeacher() && DB::getSession()->getTeacherObject()->isFachschaftsleitung())
            $absenzen .= $this->getMenuItem("respizienz", "Digitale Respizienz Fachschaftsleitung", "fa fa-briefcase", ['mode' => 'fachbetreuer']);
        if(DB::getSession()->isTeacher() && DB::getSession()->getTeacherObject()->isSchulleitung())
            $absenzen .= $this->getMenuItem("respizienz", "Digitale Respizienz Schulleitung", "fa fa-briefcase", ['mode' => 'schulleitung']);
    }
    
    
    if($this->isActive("absenzenschueler") && (DB::getSession()->isPupil() || DB::getSession()->isEltern())) {
    	$absenzen .= $this->getMenuItem("absenzenschueler", "Meine Absenzen", "fa fa-bed");
    }
    
    /* if($this->isActive("beurlaubung") && beurlaubung::userHasAccess(DB::getSession()->getUser())) {
      $absenzen .= $this->getMenuItem("beurlaubung", "Beurlaubungsantrag", "fa fa-sun-o");
    } */

    $this->html .= $absenzen;

    if(DB::isLoggedIn() && DB::getSession()->isAdmin()) {
      $this->html .= $this->getMenuItem("administration", "Administration", "fa fa-cogs");
    }


    $this->html .= "<br /><br />";

  }

  public function getHTML() {
    return $this->html;
  }

  /**
   *
   * @param unknown $page
   * @param unknown $title
   * @param unknown $icon
   * @param String[] $addParams
   * @return string
   */
  private function getMenuItem($page, $title, $icon, $addParams = []) {
    $isActive = false;

    $addParamString = "";
    if(sizeof($addParams) == 0) {
      if($_REQUEST['page'] == $page) $isActive = true;
    }
    else {
      foreach ($addParams as $name => $value) {
        if($addParamString == 0) $addParamString = "&";
        $addParamString .= $name . "=" . $value;
        if($_REQUEST[$name] == $value) $isActive = true;
        else $isActive = false;
      }

      if($_REQUEST['page'] == $page && $isActive) $isActive = true;
      else $isActive = false;
    }

    return '<li' . (($isActive)?(" class=\"active\""):("")) . '><a href="index.php?page=' . $page . $addParamString . '"><i class="' . $icon . '"></i><span> ' . $title . '</span></a></li>';
  }

  private function startDropDown($pages, $title, $icon, $addParams = []) {

    $active = in_array($_REQUEST['page'],$pages);

    if(sizeof($addParams) > 0) {
      foreach ($addParams as $name => $value) {
        if(is_array($value) && $active) {
          $active = in_array($_REQUEST[$name], $value);
        }
      }
    }

    return '<li class="' . (($active) ? ("active ") : ("")) . 'treeview">
              <a href="#">
                <i class="' . $icon . '"></i> <span>' . $title . '</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">';
  }

  public function endDropDown() {
    return '</ul></li>';
  }

  public function isActive($page) {
    return AbstractPage::isActive($page);
  }

  public static function siteIsAlwaysActive() {
    return true;
  }

}



?>
