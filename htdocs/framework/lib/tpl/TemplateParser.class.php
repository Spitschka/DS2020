<?php

class TemplateParser {
	
	public function __construct() {
		
	}

	/**
	* Kompiliert Template Source Code
	*
	* @param string code template sourcecode
	* @return string compiled templatecode
	*
	*/
	public function parse($code) {
		
		$code = addcslashes($code, '"\\');
		
		$code = preg_replace('!</then>(\s*)</if>!i', '</then><else></else>\\1</if>', $code);
		
		$code = preg_replace_callback(
				'!<if\((.*)\)>!siU',
				function($m) { return '" . ((' .$this->stripSlashes($m[1]). ') '; },
				$code);
		
		
		$code = preg_replace('!</if>!i', ')."', $code);
		
		$code = preg_replace('!<then>!i', '? ("', $code);
		
		$code = preg_replace('!</then>!i', '") ', $code);
		
		$code = preg_replace('!<else>!i', ': ("', $code);
		
		$code = preg_replace('!</else>!i', '")', $code);
		
		$code = preg_replace_callback(
				'!<expression>(.*)</expression>!siU', 
				function($m) { return '" . ' . $this->stripSlashes($m[1]) . ' . "'; },
				$code
		);
		
		
		
		return $code;
	}

	private function stripSlashes($code) {
		//$code = str_replace('\$', '$', $code);
		$code = str_replace('\\\\', '\\', $code);
		$code = str_replace('\"', '"', $code);
		$code = str_replace('\"', '"', $code);
		
		return $code;
	}

}
?>