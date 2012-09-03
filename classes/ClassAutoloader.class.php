<?php
/**
 * Autoload needed classes
 * @author etessore
 *
 */
class ClassAutoloader {
	var $loading_template;
	
	public function __construct() {
		$this->set_loading_template(dirname(__FILE__) . '/%classname%.class.php');
		spl_autoload_register(array($this, 'loader'));
	}
	
	/**
	 * Set the loading template
	 * @param string $tpl the template
	 * @return ClassAutoloader $this for chainability
	 */
	public function set_loading_template($tpl){
		$this->loading_template = $tpl;
		return $this;
	}
	
	/**
 	 * Autoload needed classes
	 * @param String $className the name of the class
	 * @return ClassAutoloader $this for chainability
	 */
	private function loader($className) {
		$filename = str_replace(
				array('%classname%'), 
				array($className), 
				$this->loading_template
		);
		if(file_exists($filename)) include_once $filename;
		return $this;
	}
}
