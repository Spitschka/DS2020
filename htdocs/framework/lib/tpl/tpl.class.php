<?php

/**
 * Template Klasse
 * @author Christian Spitschka
 *
 */
class tpl {
	/**
	 * Wenn true, dann Entwicklermodus: Templates werden immer geparst! (Aber zur Scriptlaufzeit auch nur einmal)
	 * Ist der Entwicklermodus aus, dann werden die Templates in der Datenbank hinterlegt.
	 * @var boolean An/Aus (true/false)
	 */
	private $isDevelopment = true;
	
	private $templateCache = array();
	
	/**
	 * Der Template Compiler
	 * @var TemplateParser
	 */
	private $templateCompiler;
	
	public function __construct() {
		$this->templateCompiler = new TemplateParser();
	}
	
	public function get($name) {
		if(isset($this->templateCache[$name])) {
			return $this->templateCache[$name];
		}
		
		
		$templateContent = DB::getDB()->query_first("SELECT * FROM templates WHERE templateName LIKE '" . $name . "'");
		if($templateContent['templateCompiledContents'] != "") {
			if(!$this->isDevelopment) return $templateContent['templateCompiledContents'];
		}
		
		if(DB::isDebug()) {
			if(!file_exists("../framework/templates/$name.htm")) {
				new errorPage("Ein angefordertes Template ist nicht verfügbar: " . $name);
				exit(0);
			}
		}
	
		$this->templateCache[$name] = $this->templateCompiler->parse(implode("",file("../framework/templates/$name.htm")));
		
		DB::getDB()->query("INSERT INTO templates
				(templateName, templateCompiledContents)
				values(
					'" . DB::getDB()->escapeString($name) . "',
					'" . DB::getDB()->escapeString($this->templateCache[$name]) . "'
				) ON DUPLICATE KEY UPDATE templateCompiledContents='" . DB::getDB()->escapeString($this->templateCache[$name]) . "'
				");
		
		
		
		return $this->templateCache[$name];
	}
	
	public function out($string) {
		echo($string);
	}
}

?>