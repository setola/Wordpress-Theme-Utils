<?php
include_once dirname(__FILE__) . '/classes/ClassAutoloader.class.php';;

global $autoloader;
$autoloader = new ClassAutoloader();



/**
 * This is experimental code :)
 * w3 total cache hook to enable symlinked files to be minified.
 */
add_filter('w3tc_custom_minapp_options', 'allow_dirs', 10, 1);
function allow_dirs($arr){
	$arr['allowDirs'] = array_merge((array)$arr['allowDirs'], array('/usr/local/fastbooking/'));
	return $arr;
}

/**
 * PECT_HTTP is usually missing...
 * @param array $parsed_url the array to be merged
 * @return string the url
 */
if(!function_exists('http_build_url ')){
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
register_nav_menu('primary', __('Primary Menu', ThemeHelpers::textdomain));
register_nav_menu('secondary', __('Secondary Menu', ThemeHelpers::textdomain));


/**
 * Hide the wp admin bar
 */
add_filter('show_admin_bar', '__return_false');

/**
 * Hook to body class
 */
add_filter('body_class', array('ThemeHelpers', 'body_class'));

/**
 * Hook to wp_head
 */
add_action('wp_head', array('ThemeHelpers', 'head'));

/**
 * Image sizes for this theme
 */
add_theme_support('post-thumbnails');
add_image_size('slideshow', 640, 330, false);

/**
 * Remove some useless css and js by wpml
 */
define(ICL_DONT_LOAD_LANGUAGES_JS, true);
define(ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS, true);
define(ICL_DONT_LOAD_NAVIGATION_CSS, true);

//new SpecialOffersSnippetAjax();
