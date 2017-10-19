<?php
ini_set("memory_limit","-1");

include_once './mainsettings.php';

include('./startup.php');

// error_reporting(E_ERROR);

if($_SERVER['SERVER_PORT'] != 443 && false) {			// Wir wollen SSL erzwingen!
  header("Location: https://www.rsu-ssl.de");
}

new apihandler((isset($_REQUEST['page']) && $_REQUEST['page'] != "") ? $_REQUEST['page'] : 'index');

?>