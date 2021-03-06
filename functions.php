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

/**
 * Automatic initialization for the framework settings
 * @package core
 * @version 1.0.0
 */
function wordpress_theme_utils_initialize_hook(){
	include_once 'classes/ThemeUtils.class.php';
	wtu_init();
	//ThemeHelpers::remove_wpml_assets();
	//add_theme_support( 'post-thumbnails' );
	//add_image_size('slideshow', 940, 400, true);
}
add_action('after_setup_theme', 'wordpress_theme_utils_initialize_hook', 9);



if(!function_exists('wordpress_theme_utils_credits')){
	/**
	 * Fill the default credit line in the footer
	 * @return string
	 */
	function wordpress_theme_utils_credits(){
		$toret = array();
		$toret[] = HtmlHelper::anchor(
			home_url('/'), 
			sprintf(__('&copy; %s', 'wordpress_theme_utils'), get_bloginfo('name'))
		);
		$toret[] = HtmlHelper::anchor(
			__('https://github.com/setola/Wordpress-Theme-Utils/', 'wordpress_theme_utils'), 
			sprintf(__('Built on %s', 'wordpress_theme_utils'), 'WordPress Themes Utils')
		);
		$toret[] = HtmlHelper::anchor(
			__('http://wordpress.org/', 'wordpress_theme_utils'), 
			sprintf(__('Powered by %s', 'wordpress_theme_utils' ), 'WordPress')
		);
		
		echo HtmlHelper::unorderd_list($toret, array('class'=>'linear-menu clearfix'));
	}
	add_action('wtu_credits', 'wordpress_theme_utils_credits');
}





if(!function_exists('get_called_class')){
    /**
     * PHP 5.2 fix for non existing get_called_class()
     */
    function get_called_class($functionName=null){
        $btArray = debug_backtrace();
        $btIndex = count($btArray) - 1;
        while($btIndex > -1){
            if(!isset($btArray[$btIndex]['file'])){
                $btIndex--;
                if(isset($matches[1])){
                    if(class_exists($matches[1])){
                        return $matches[1];
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            } else {
                $lines = file($btArray[$btIndex]['file']);
                $callerLine = $lines[$btArray[$btIndex]['line']-1];
                if(!isset($functionName)) {
                    preg_match('/([a-zA-Z\_]+)::/',
                        $callerLine,
                        $matches);
                } else {
                    preg_match('/([a-zA-Z\_]+)::'.$functionName.'/',
                        $callerLine,
                        $matches);
                }
                $btIndex--;
                if(isset($matches[1])){
                    if(class_exists($matches[1])){
                        return $matches[1];
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            }
        }
        return $matches[1];
    }
}

if(!function_exists('http_build_url')){
    /**
     * Build a URL.
     *
     * The parts of the second URL will be merged into the first according to the flags argument.
     * PECT_HTTP is usually missing on many environment.
     * This function provides the same feature as the function included in PECT_HTTP.
     * @see @link http://php.net/manual/en/function.http-build-url.php
     * @param array $parsed_url the array to be merged
     * @package core
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

