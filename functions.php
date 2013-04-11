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


function wordpress_theme_utils_initialize(){
	include_once 'classes/ThemeUtils.class.php';
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
	
	/**
	 * Initialize the main menues
	 */
	if(WORDPRESS_THEME_UTILS_REGISTER_TOP_MENU === true)
		register_nav_menu('primary', __('Primary Menu', 'theme'));
	if(WORDPRESS_THEME_UTILS_REGISTER_BOTTOM_MENU === true)
		register_nav_menu('secondary', __('Secondary Menu', 'theme'));
	
}
add_action('after_setup_theme', 'wordpress_theme_utils_initialize', 9);




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










// TODO: move this into ThemeHelpers to allow overloading in child theme 



if(!function_exists('the_html')) { 
	/**
	 * Print the <html> opening tag from html5 boilerplate
	 * @param string|array $class some additional classes
	 */
	function the_html($class=''){
		echo HtmlHelper::open_html($class);
	}
}

