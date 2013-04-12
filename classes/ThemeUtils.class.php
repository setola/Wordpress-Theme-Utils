<?php 
/**
 * Stores the code for the parent theme setting initialization and management
 */


/**
 * Initializes and manages the parent theme config and constants
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class ThemeUtils{
	/**
	 * Stores the unique instance
	 * @var ThemeUtils
	 */
	private static $instance = null;

	/**
	 * Initializes default settings
	 * Singleton private constructor
	 */
	private function __construct(){
		self::default_constants();
		self::enable_autoload_system();
		self::disable_debug();
		self::$instance = $this;
	}

	/**
	 * Gets the instance with the current options set
	 * @return ThemeUtils current instance
	 */
	public static function get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initializes the autoloader subsystem
	 */
	public static function enable_autoload_system(){
		include_once WORDPRESS_THEME_UTILS_PATH . WORDPRESS_THEME_UTILS_AUTOLOADER_RELATIVE_PATH;
		new ClassAutoloader();
	}

	/**
	 * Registers the Primary Menu to WordPress
	 */
	public static function register_main_menu(){
		register_nav_menu('primary', __('Primary Menu', 'theme'));
	}

	/**
	 * Register the Secondary Menu to WordPress
	 */
	public static function register_bottom_menu(){
		register_nav_menu('secondary', __('Secondary Menu', 'theme'));
	}

	/**
	 * Enables vd()\vc()\v() functions output,
	 * This is generically good for development environments.
	 */
	public static function enable_debug(){
		$debug = DebugUtils::get_instance();
		$debug->status = true;
	}


	/**
	 * Disable vd()\vc()\v() functions output,
	 * This is default status, generically good for production environments.
	 */
	public static function disable_debug(){
		$debug = DebugUtils::get_instance();
		$debug->status = false;
	}

	/**
	 * Register some constants
	 */
	public static function default_constants(){
		// initialize constants only once
		if(defined(WORDPRESS_THEME_UTILS_PATH)) return;

		/**
		 * The base path for Wordpress Theme Utils
		 */
		if(!defined('WORDPRESS_THEME_UTILS_PATH'))
			define('WORDPRESS_THEME_UTILS_PATH', dirname(__DIR__));

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
	}
}


/**
 * Initializes the WordPress Theme Utils.
 * Use this in your child theme functions.php
 */
function wtu_init(){
	ThemeUtils::get_instance();

	/**
	 * Register some standard assets
	 *
	 * overload the global $assets variable in your child theme functions.php if you need customization on this.
	 * @see DefaultAssets for adding or remove assets
	*/
	global $assets;
	if(empty($assets)){
		new DefaultAssetsCDN();
	}

	/**
	 * Register runtime infos, useful for javascript
	 *
	 * Overload the global $runtime_infos in your child theme functions.php if you need customization on this.
	 * @see RuntimeInfos for more details
	 */
	global $runtime_infos;
	if(empty($runtime_infos)){
		$runtime_infos = new RuntimeInfos();
		$runtime_infos->hook();
	}
}