<?php 

$classes = [
    'externalRESTapi' => [
        'ExternalPortalRESTapi'
    ],
    'portalapi' => [
        'PortalApi'
    ],
	'cron' => [
		'AbstractCron',
		'SyncSchuelerData',
	    'SyncLehrer',
	    'SyncFaecher'
	],
	'data' => [
		'absenzen',
		'eltern',
		'grade',
	    'fach',
		'klasse',
		'lehrer',
		'pupil',
		'schueler',
		'SchuelerAdresse',
		'SchuelerTelefonnummer',
		'SchuelerElternEmail',
		'session'
	],
    'data/absenzen' => [
        'AbsenzBeurlaubungAntrag'
    ],
	'data/termine' => [
		'Leistungsnachweis',
		'AbstractTermin'
	],
	'data/user' => [
		'user'
	],
    'data/respizienz' => [
        'LeistungsnachweisRespizienz'
    ],
	'db' => [
		'mysql',
	],
	'email' => [
		'cert',
		'email',
		'phpmailer',
	],
	'exception' => [
		'DbException',
	],
	'GarbageCollector' => [
		'GarbageCollector',
	],
	'menu' => [
		'menu',
	],

	'system' => [
		'cronhandler',
	    'resthandler',
		'DateFunctions',
		'DB',
		'functions',
		'requesthandler',
		'settings',
		'Debugger',
		'Encoding'
	],
	'tpl' => [
		'TemplateParser',
		'tpl'
	],
	'print' => [
		'PrintNormalPageA4WithHeader'
	],
	'uploads' => [
		'FileUpload'
	]
];


function myAutoLoaderImpl($class) {
	global $classes;
	
	include_once("../framework/lib/system/requesthandler.class.php");
	
	
	if(in_array($class, requesthandler::getAllowedActions())) return;		// Seiten nicht automatisch laden, macht der Requesthandler

	if($class == 'TCPDF') {
		include_once '../framework/lib/tcpdf/tcpdf.php';
		return;
	}
	
	if(strtolower($class) == "phpmailer") {
		include_once '../framework/lib/email/phpmailer/class.phpmailer.php';
		include_once '../framework/lib/email/phpmailer/class.smtp.php';
		return;
	}
	
	if($class == "AbstractPage") return;
	
	foreach($classes as $c => $d) {
		for($i = 0; $i < sizeof($d); $i++) {
			if(strtolower($d[$i]) == strtolower($class)) {
				include_once('../framework/lib/' . $c . '/' . $d[$i] . ".class.php");
				return;
			}
		}
	}
	
	// Unbekannte Klasse
	
	Debugger::debugObject("<h1>" . $class . " not found</h1>", true);
}
