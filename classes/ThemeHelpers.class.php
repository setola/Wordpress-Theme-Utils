<?php 
/**
 * stores the definition for class ThemeHelpers
 */
/**
 * Stores some static methods that may be useful for html management
 * @author etessore
 * @version 1.0.5
 * 
 * Changelog
 * 1.0.5
 * 	added support for title in 404 pages
 * 1.0.4 
 * 	moved some html dom functions to HtmlHelper class
 * 1.0.3
 * 	added support for FB Seo plugin
 * 1.0.2
 * 	code refactoring and some minor design modifications
 * 1.0.1
 * 	do not remember, sorry
 * 1.0.0
 * 	first release
 */
class ThemeHelpers{
	/**
	 * @var string Stores the name of the gettext domain for this theme
	 */
	const textdomain = 'theme';
	
	/**
	 * This filter callback adds some usefull classes to the body
	 * Remember to call body_class() in the theme!
	 * 
	 * @param array $classes the classes already added by wordpress or some other plugin
	 */
	public static function body_class($classes){
		if(function_exists('fb_get_systags')){
			$tags = fb_get_systags(get_the_ID(), false);
		} else {
			$tags = array();
		}
		foreach($tags as $k => $tag){
			$tags[$k] = 'tag-'.$tag;
		}
		$classes = array_merge($classes, $tags);
		if(is_super_admin()) $classes[] = 'super-admin';
		if(!is_user_logged_in()) $classes[] = 'logged-out';
		
		return $classes;
	}
	
	/**
	 * Enqueue a JavaScript in WordPress init action
	 * @see @link http://codex.wordpress.org/Function_Reference/wp_register_script
	 * according to this we have to:
	 * Use the wp_enqueue_scripts action to call this function, 
	 * or admin_enqueue_scripts to call it on the admin side. 
	 * Calling it outside of an action can lead to problems. 
	 * 
	 * @see @link http://core.trac.wordpress.org/ticket/11526 #11526 for details.
	 * @param string $handle Script name
	 * @param string $src Script url
	 * @param array $deps (optional) Array of script names on which this script depends
	 * @param string|bool $ver (optional) Script version (used for cache busting), set to NULL to disable
	 * @param bool $in_footer (optional) Whether to enqueue the script before </head> or before </body>
	 */
	public static function load_js($handle, $src = null, $deps = array(), $ver = null, $in_footer = false){
		// anonimous functions are available only from 5.3.0
		if(version_compare(PHP_VERSION, '5.3.0', '>=')){
			wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
			return;
		}
		
		add_action('wp_enqueue_scripts', function($handle, $src, $deps, $ver, $in_footer){
			wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
		});
	}
	
	/**
	 * Enqueue a StyleSheet in WordPress init action
	 * @see @link http://codex.wordpress.org/Function_Reference/wp_register_style
	 * according to this we have to:
	 * Use the wp_enqueue_scripts action to call this function. 
	 * Calling it outside of an action can lead to problems. 
	 * @see @link http://core.trac.wordpress.org/ticket/17916 #17916 for details.
	 * 
	 * @param string $handle Name of the stylesheet.
	 * @param string|bool $src Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param array $deps Array of handles of any stylesheet that this stylesheet depends on.
	 *  (Stylesheets that must be loaded before this stylesheet.) Pass an empty array if there are no dependencies.
	 * @param string|bool $ver String specifying the stylesheet version number. Set to NULL to disable.
	 *  Used to ensure that the correct version is sent to the client regardless of caching.
	 * @param string $media The media for which this stylesheet has been defined.
	 */
	public static function load_css($handle, $src = null, $deps = array(), $ver = null, $media = false){
		if(version_compare(PHP_VERSION, '5.3.0', '>=')){
			wp_enqueue_style($handle, $src, $deps, $ver, $media);
			return;
		}
		
		add_action('wp_enqueue_scripts', function($handle, $src, $deps, $ver, $media){
			wp_enqueue_style($handle, $src, $deps, $ver, $media);
		});
	}
	
	/**
	 * Remove some useless css and js by wpml
	 * @see @link http://wpml.org/documentation/support/wpml-coding-api/
	 */
	public static function remove_wpml_assets(){
		define('ICL_DONT_LOAD_LANGUAGES_JS', true);
		define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
		define('ICL_DONT_LOAD_NAVIGATION_CSS', true);
	}

	/**
	 * Merges some images to a single big one to save some http connections.
	 * Stores it in a cache folder.
	 * To disable the cache system set IMAGE_MERGE_FORCE_REFRESH constant to true
	 * @param array $images an array of images: for every element $image['path'] and $image['url'] have to be defined
	 * @param array $config timthumb config, default 'w'=>'700', 'h'=>'370', 'q'=>'50', 'r'=>false
	 * @return string the url for the big image
	 */
	public static function merge_images($images, $config=null){
		if(empty($images)) return 'No images';
		$config = array_merge(
			array(
				'w'		=>	'700',
				'h'		=>	'370',
				'q'		=>	'50',
				'r'		=>	false
			),
			$config
		);
	
		$combined_image = imagecreatetruecolor($config['w']*count($images), $config['h']);
	
		$cache_name = '';
		foreach($images as $image){
			$cache_name .= $image['path'].';';
		}
		$cache_name .= serialize($config);
		$cache_name = md5($cache_name);
	
		$cache_dir = get_template_directory().'/cache/';
		if (!@is_dir($cache_dir)){
			if (!@mkdir($cache_dir)){
				die('Couldn\'t create cache dir: '.$cache_dir);
			}
		}
		$cache_url = get_bloginfo('template_url').'/cache/'.$cache_name.'.jpg';
		$cache_path = $cache_dir.$cache_name.'.jpg';
	
		if(
			!file_exists($cache_path)
			|| IMAGE_MERGE_FORCE_REFRESH===true
			|| (
					get_option('development_mode','no') == 'yes'
					&& $_GET['forcerefresh'] == 'true'
			)
		){
			foreach($images as $array_index => $image){
				$src = $image['url'].'?'.http_build_query($config, '', '&');
	
				$info = getimagesize($src);
				switch($info['mime']){
					case 'image/jpeg':
						$image = imagecreatefromjpeg($src);
						break;
					case 'image/png':
						$image = imagecreatefrompng($src);
						break;
					case 'image/gif':
						$image = imagecreatefromgif($src);
						break;
					default:
						die('unknow mime type');
				}
	
				imagecopymerge(
					$combined_image,
					$image,
					$array_index*$config['w'],
					0, 0, 0,
					$config['w'],
					$config['h'],
					100
				);
	
				imagejpeg(
					$combined_image,
					$cache_path,
					$config['q']
				);
			}
		}
	
		return $cache_url;
	}
	
	/**
	 * Returns the content after the more tag for the given post
	 * @param string $more_link_text Optional. Content for when there is more text
	 * @param int|object $post_id Post ID or post object.
	 * @param bool $apply_filters true if you want the_content filter applyed on the return value
	 */
	static function get_the_content_after_more($more_link_text = '', $post_id = null, $apply_filters = true){
		$toret = '';
		
		if(isset($post_id)){
			global $post;
			$post = get_post($post_id);
			setup_postdata($post);
			$toret = get_the_content($more_link_text, true);
			wp_reset_postdata();
		} else {
			$toret = get_the_content($more_link_text, true);
		}
		
		if($apply_filters){
			$toret = apply_filters('the_content', $toret);
			$toret = str_replace(']]>', ']]&gt;', $toret);
		}
		
		return $toret;
	}
	
	/**
	 * Prints the content before the more tag for the given post
	 * If the tag is not present it will print the entire body
	 * @param string $more_link_text Optional. Content for when there is more text
	 * @param int|object $post_id Post ID or post object.
	 * @param bool $apply_filters true if you want the_content filter applyed on the return value
	 */
	static function get_the_content_before_more($more_link_text = '', $post_id = null, $apply_filters = true){
		global $more, $post;
		
		$swap = $more;
		$more = 0;
		
		if(isset($post_id)){
			$post = get_post($post_id);
			setup_postdata($post);
			
			$toret = get_the_content($more_link_text, false);
			wp_reset_postdata();
		} else {
			$toret = get_the_content($more_link_text, false);
		}
		
		$more = $swap;
		
		if($apply_filters){
			$toret = apply_filters('the_content', $toret);
			$toret = str_replace(']]>', ']]&gt;', $toret);
		}
		
		return $toret;
	}
	
	/**
	 * Checks if the current post has the <!--more--> tag
	 */
	static function has_more_tag(){
		global $post;
		return strpos($post->post_content, '<!--more-->')!==false;
	}
	
	/**
	 * Retrieves the correct DOCTYPE
	 * @param string $type the type, default is html5
	 */
	static function doctype($type = 'html5') {
		_deprecated_function(__CLASS__.'::'.__FUNCTION__, '1.0.4', 'HtmlHelper::doctype');
		$doctypes = array(
			'html5'			=> '<!DOCTYPE html>',
			'xhtml11'		=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
			'xhtml1-strict'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			'xhtml1-trans'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'xhtml1-frame'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
			'html4-strict'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
			'html4-trans'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
			'html4-frame'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		);
	
		if (isset($doctypes[$type])) {
			return $doctypes[$type] . "\n";
		}
		return '';
	}
	
	/**
	 * Get the markup for an <a> tag
	 * @return string the markup for an html <a> tag
	 * @param string $href the url to be pointed
	 * @param string $label the text
	 * @param array|string $parms some html attributes in key=>value pairs or a plain string
	 */
	public static function anchor($href, $label, $parms=''){
		_deprecated_function(__CLASS__.'::'.__FUNCTION__, '1.0.4', 'HtmlHelper::anchor');
		$href 	= esc_attr($href);
		//$label 	= esc_html($label); //i want the possibility to insert an inner <img>
		$parms 	= self::params($parms);
		return <<< EOF
		<a href="$href"$parms>$label</a>
EOF;
	}
	
	/**
	 * Get the markup for a <img> tag
	 * @param string $src the image source
	 * @param array|string $parms additional parameters
	 */
	public static function image($src, $parms=''){
		_deprecated_function(__CLASS__.'::'.__FUNCTION__, '1.0.4', 'HtmlHelper::image');
		$src = esc_attr($src);
		$parms 	= self::params($parms);
		return <<< EOF
		<img src="$src"$parms >
EOF;
	}
	
	/**
	 * Retrieves a <script> tag
	 * @deprecated
	 * @param string $content the inner content
	 * @param array $parms some parameters
	 */
	public static function script($content, $parms=''){
		_deprecated_function(__CLASS__.'::'.__FUNCTION__, '1.0.4', 'HtmlHelper::script');
		$parms 	= self::params($parms);
		return <<< EOF
	<script type="text/javascript"$parms>
		$content
	</script>
EOF;
	}
	
	/**
	 * Generates HTML Node Attribures
	 * @param string $glue
	 * @param array|string $pieces
	 * @return string
	 * @author http://blog.teknober.com/2011/04/13/php-array-to-html-attributes/
	 */
	public static function array_to_html_attributes($glue, $pieces) {
		_deprecated_function(__CLASS__.'::'.__FUNCTION__, '1.0.4');
		$str = $pieces;
		if (is_array($pieces)) {
			$str = " ";
			foreach($pieces as $key => $value) {
				if (strlen($value) > 0) {
					$str .= esc_attr($key) . esc_attr($glue) . '"' . esc_attr($value) . '" ';
				}
			}
		}
	
		return rtrim($str);
	}
	
	/**
	 * Prepare the $parms to be printed as html attributes
	 * @param string|array $parms list of html attributes
	 */
	public static function params($parms=''){
		_deprecated_function(__CLASS__.'::'.__FUNCTION__, '1.0.4');
		$parms 	= trim(self::array_to_html_attributes('=', $parms));
		if(!empty($parms)) $parms = ' '.$parms;
		return $parms;
	}
	
	
	/**
	 * Retrieves the url from array $url_arr
	 * If defined use http_build_url, else custom code
	 * @param array $url_arr the array of attributes 
	 * @see http://php.net/manual/en/function.parse-url.php
	 */
	public static function build_url($url_arr){
		if(function_exists('http_build_url')){
			return  http_build_url($url_arr);
		} else {
			$scheme   = isset($url_arr['scheme']) ? $url_arr['scheme'] . '://' : '';
			$host     = isset($url_arr['host']) ? $url_arr['host'] : '';
			$port     = isset($url_arr['port']) ? ':' . $url_arr['port'] : '';
			$user     = isset($url_arr['user']) ? $url_arr['user'] : '';
			$pass     = isset($url_arr['pass']) ? ':' . $url_arr['pass']  : '';
			$pass     = ($user || $pass) ? "$pass@" : '';
			$path     = isset($url_arr['path']) ? $url_arr['path'] : '';
			$query    = isset($url_arr['query']) ? '?' . http_build_query($params) : '';
			$fragment = isset($url_arr['fragment']) ? '#' . $url_arr['fragment'] : '';
			return  "$scheme$user$pass$host$port$path$query$fragment";
		}
	}
	
	/**
	 * Retrives the markup for the default seo heading (h1+span) 
	 */
	public static function heading(){
		$h1		=	self::get_the_seo_h1();
		$extra	=	self::get_the_seo_span();
		return <<< EOF
	<h1>$h1</h1>
	<span>$extra</span>
		
EOF;
	}
	
	/**
	 * Returns the title of the page.
	 * Choose in order from: 
	 * FB SEO Plugin
	 * YOAST Seo TODO
	 * Blog Name
	 */
	public static function get_the_seo_title(){
		if(is_404()){
			return __('Error 404', 'theme');
		}
		if(function_exists('fbseo_get_title')) 
			return fbseo_get_title();
		
		return get_option('blogname');
	}
	
	/**
	 * Returns the description of the page
	 * Choose in order from:
	 * FB SEO Plugin
	 * YOAST Seo TODO
	 * Blog Name
	 */
	public static function get_the_seo_description(){
		if(is_404()){
			return __('Error 404', 'theme');
		}
		
		if(function_exists('fbseo_get_metadescription'))
			return fbseo_get_metadescription();
		
		return get_the_excerpt();
	}
	
	/**
	 * Returns the H1 for this page
	 * Choose in order from:
	 * FB SEO Plugin
	 * Post Title
	 * @param $post int|object the post
	 */
	public static function get_the_seo_h1($post = null){
		if(is_null($post)){
			global $post;
		}
		if(is_404()){
			return __('Error 404', 'theme');
		}
		if(is_numeric($post)){
			$id = $post;
			global $post;
			$post = get_post($id);
		}
		if(function_exists('fbseo_get_h1')){
			//fbseo_get_h1();
			global $fbseoManager,$post;
			return $fbseoManager->getSeo($post, 'h1');
		}
		
		return get_the_title();
	}
	
	/**
	 * Returns the SPAN for this page
	 * Choose in order from:
	 * FB SEO Plugin
	 * Post Title
	 * @param $post int|object the post
	 */
	public static function get_the_seo_span($post = null){
		if(is_null($post)){
			global $post;
		}
		if(is_404()){
			return __('page not found');
		}
		if(is_numeric($post)){
			$id = $post;
			global $post;
			$post = get_post($id);
		}
		if(function_exists('fbseo_get_h1_extra')){
			//fbseo_get_h1_extra();
			global $fbseoManager;
			return $fbseoManager->getSeo($post, 'h1_extra');
		}
		
		return get_option('blogdescription');
	}
		
	/**
	 * Retrieves the markup for the Add This widget
	 */
	public static function add_this(){
		wp_enqueue_script(
				'addthis', 
				'http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-502116d244d86b08',
				null,
				null,
				true
		);
		return <<< EOF
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style ">
	<a class="addthis_button_facebook"></a>
	<a class="addthis_button_google_plusone"></a>
	<a class="addthis_button_twitter"></a>
	<a class="addthis_button_compact"></a>
	<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-502116d244d86b08"></script>
	<!-- AddThis Button END -->	
EOF;
	}


}


