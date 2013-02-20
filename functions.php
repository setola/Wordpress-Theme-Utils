<?php
/**
 * Theme functions and definitions
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyten_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 */

if(!defined('WORDPRESS_THEME_UTILS_PATH')) 
	define('WORDPRESS_THEME_UTILS_PATH', dirname(__FILE__));

if(!defined('WORDPRESS_THEME_UTILS_DEBUG'))
	define('WORDPRESS_THEME_UTILS_DEBUG', true);

include_once WORDPRESS_THEME_UTILS_PATH . '/classes/ClassAutoloader.class.php';

/**
 * Initialize the autoloader
 */
new ClassAutoloader();

/**
 * Initialize the debug utils vd() v() vc()
 */
new DebugUtils();


/**
 * Register some standard assets
 */
//new DefaultAssets();


/**
 * Runtime infos
 */
global $runtime_infos;
$runtime_infos = new RuntimeInfos();
$runtime_infos->hook();




if(!function_exists('http_build_url')){
/**
 * PECT_HTTP is usually missing on many environment.
 * This function provides the same feature as the function included in PECT_HTTP.
 * @see @link http://php.net/manual/en/function.http-build-url.php
 * @param array $parsed_url the array to be merged
 * @return string the url
 */
	function http_build_url($parsed_url) {
		$scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
		$host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
		$port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
		$user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
		$pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : '';
		$pass     = ($user || $pass) ? "$pass@" : '';
		$path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
		$query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
		$fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
		return "$scheme$user$pass$host$port$path$query$fragment";
	}
}


/**
 * Initialize the main menues
 */
register_nav_menu('primary', __('Primary Menu', 'theme'));
register_nav_menu('secondary', __('Secondary Menu', 'theme'));


/**
 * Hide the wp admin bar
 */
add_filter('show_admin_bar', '__return_false');

/**
 * Hook to body class
 */
add_filter('body_class', array('ThemeHelpers', 'body_class'));







// TODO: move this into ThemeHelpers to allow overloading in child theme 



if(!function_exists('the_html')) { 
/**
 * Print the <html> opening tag from html5 boilerplate
 * @param string|array $class some additional classes
 */
	function the_html($class=''){
		if(is_array($class)){
			$class = ' '.join(' ', $class);
		}
		$class = ' '.trim($class);
		?>
		<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7<?php echo $class; ?>" <?php language_attributes(); ?>> <![endif]-->
		<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8<?php echo $class; ?>" <?php language_attributes(); ?>> <![endif]-->
		<!--[if IE 8]>         <html class="no-js lt-ie9<?php echo $class; ?>" <?php language_attributes(); ?>> <![endif]-->
		<!--[if gt IE 8]><!--> <html class="no-js<?php echo $class; ?>" <?php language_attributes(); ?>> <!--<![endif]-->
		<?php 
	}
}

