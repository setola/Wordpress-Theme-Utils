<?php
/**
 * Contains the ClassAutoloader class definitions
 */

/**
 * Autoload needed classes
 * @author etessore
 * @version 1.0.1
 * @package classes
 */
 
/* 
 * Changelog:
 * 1.0.1
 * 	support multiple search path to allow children theme to extend classes
 * 1.0.0
 * 	Initial release
 */
class ClassAutoloader {
	const WORDPRESS_THEME_UTILS_CLASS_DIR = 'classes';
	
	/**
	 * @var array stores the path the system will scan for files
	 */
	public $loading_template;
	
	/**
	 * Initializes the autoloader
	 */
	public function __construct() {
		$this
			// First search in the child theme dir
			->add_loading_template(
				get_stylesheet_directory().'/'
				.self::WORDPRESS_THEME_UTILS_CLASS_DIR
				.'/%classname%.class.php'
			)
			// then search into the parent theme dir
			->add_loading_template(
				get_template_directory().'/'
				.self::WORDPRESS_THEME_UTILS_CLASS_DIR
				.'/%classname%.class.php'
			)
			->register_autoload();
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
