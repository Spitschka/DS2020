<?php


abstract class AbstractCron {

	public function __construct() {
		
	}
	
	public abstract function execute();
	
	public abstract function getName();
	
	public abstract function getDescription();
		
	/**
	 * 
	 * 
	 * @return ['success' => 'true/false', 'resultText' => 'Text, der in der Administration angezeigt wird.']
	 */
	public abstract function getCronResult();
	
	public abstract function informAdminIfFail();
	
	public abstract function executeEveryXSeconds();
}	

?>