<?php 

class ThemeUtils{
	private static $instance = null;
	
	private function __construct(){

		/**
		 * The base path for Wordpress Theme Utils
		 */
		if(!defined('WORDPRESS_THEME_UTILS_PATH'))
			define('WORDPRESS_THEME_UTILS_PATH', dirname(__DIR__));
		
		/**
		 * Set to true if you want vd()\vc()\v() to print output,
		 * false is generic better for production
		*/
		if(!defined('WORDPRESS_THEME_UTILS_DEBUG'))
			define('WORDPRESS_THEME_UTILS_DEBUG', true);
		
		/**
		 * Set to false to disable registration of Top Menu
		*/
		if(!defined('WORDPRESS_THEME_UTILS_REGISTER_TOP_MENU'))
			define('WORDPRESS_THEME_UTILS_REGISTER_TOP_MENU', true);
		
		/**
		 * Set to false to disable registration of Bottom Menu
		*/
		if(!defined('WORDPRESS_THEME_UTILS_REGISTER_BOTTOM_MENU'))
			define('WORDPRESS_THEME_UTILS_REGISTER_BOTTOM_MENU', true);
		
		/**
		 * Relative path for template parts
		*/
		if(!defined('WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH'))
			define('WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH', 'partials/');
		
		/**
		 * Relative path for autoloader class
		*/
		if(!defined('WORDPRESS_THEME_UTILS_AUTOLOADER_RELATIVE_PATH'))
			define('WORDPRESS_THEME_UTILS_AUTOLOADER_RELATIVE_PATH', '/classes/ClassAutoloader.class.php');

		/**
		 * Initialize the autoloader
		 */
		include_once WORDPRESS_THEME_UTILS_PATH . WORDPRESS_THEME_UTILS_AUTOLOADER_RELATIVE_PATH;
		new ClassAutoloader();


		/**
		 * Initialize the debug utils
		 * @see vd()
		 * @see v()
		 * @see vc()
		 */
		DebugUtils::get_instance();
		
		self::$instance = $this;
	}
	
	public static function get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	public static function enable_autoload_system(){}
	
	
}