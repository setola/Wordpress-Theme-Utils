<?php
/**
 * Autoload needed classes
 * @author etessore
 *
 */
class ClassAutoloader {
	const WORDPRESS_THEME_UTILS_CLASS_DIR = 'classes';
	public $loading_template;
	
	public function __construct() {
		$this->add_loading_template(
			WORDPRESS_THEME_UTILS_PATH.'/'.self::WORDPRESS_THEME_UTILS_CLASS_DIR.'/%classname%.class.php'
		);
		//spl_autoload_register(array($this, 'loader'));
	}
	
	/**
	 * Set the loading template
	 * @param string $tpl the template
	 * @return ClassAutoloader $this for chainability
	 */
	public function add_loading_template($tpl){
		$this->loading_template[] = $tpl;
		return $this;
	}
	
	/**
	 * Hook to the WordPress init
	 */
	public function hook(){
		add_action('init', array($this, 'register_autoload'));
	}
	
	/**
	 * Register the autoload function to PHP
	 */
	public function register_autoload(){
		spl_autoload_register(array($this, 'loader'));
	}
	
	/**
 	 * Autoload needed classes
	 * @param String $className the name of the class
	 * @return ClassAutoloader $this for chainability
	 */
	private function loader($className) {
		foreach($this->loading_template as $tpl){
			$filename = str_replace(
				array('%classname%'), 
				array($className), 
				$tpl
			);
			if(file_exists($filename)) {
				include_once $filename;
				return $this;
			}
		}
		return $this;
	}
}
