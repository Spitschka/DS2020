<?php

ini_set("memory_limit","-1");


include_once './mainsettings.php';

include('./startup.php');


if($_SERVER['SERVER_PORT'] != 443 && $_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['REMOTE_ADDR'] != "::1" && !DB::isDebug()) {
    // Wir wollen SSL erzwingen!
    header("Location: " . DB::getGlobalSettings()->urlToIndexPHP);
}

new requesthandler((isset($_REQUEST['page']) && $_REQUEST['page'] != "") ? $_REQUEST['page'] : 'index');

?>