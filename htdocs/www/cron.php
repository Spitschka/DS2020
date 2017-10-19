<?php
ini_set("memory_limit","-1");

include_once './mainsettings.php';

include('./startup.php');

error_reporting(E_ALL);

new cronhandler();

?>