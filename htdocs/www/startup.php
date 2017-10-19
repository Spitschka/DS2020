<?php

include("../framework/lib/system/autoloader.php");
include("../framework/lib/system/errorhandler.php");
include("../framework/lib/vendor/autoload.php");

spl_autoload_register('myAutoLoaderImpl');

set_error_handler('myErrorHandlerImpl',E_ALL);

// Datenbank verbinden
DB::start();
session::cleanSessions();

// Garbage Collection durchführen
GarbageCollector::EveryRequest();

?>
