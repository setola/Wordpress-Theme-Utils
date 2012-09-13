<?php
/**
 * Load jQuery from the theme or w3tc will blow up
 * because of it doesn't support symlinked files
 */
if(!is_admin()){
	wp_deregister_script('jquery');
	wp_register_script('jquery', get_template_directory_uri().'/js/jquery.min.js', null, '1.7.2', true);
	wp_deregister_script('jquery-ui-core');
	wp_register_script('jquery-ui-core', get_template_directory_uri().'/js/jquery.ui.core.min.js', null, '1.7.2', true);
	wp_register_script('fancybox', get_template_directory_uri().'/js/jquery.fancybox-1.3.4.js', array('jquery'), '1.3.4', true);
	wp_register_script('fancybox-init', get_template_directory_uri().'/js/fancybox.js', array('jquery','fancybox'), '1.0', true);
	wp_register_script('cycle', get_template_directory_uri().'/js/jquery.cycle.js', array('jquery'), '2.9999.5', true);
	wp_register_script('cycle-init',get_template_directory_uri().'/js/cycle.js',array('jquery','cycle'),'1.0',true);
	wp_register_script('text-autosize', get_template_directory_uri().'/js/jquery.text-autosize.js', array('jquery'), '0.9', true);
	//wp_register_script('app', get_template_directory_uri().'/js/app.js', array('jquery'), '1.0', true);
	wp_register_script('jquery-flash',get_template_directory_uri().'/js/jquery.flash.js',array('jquery'),false,'1.0');
	wp_register_script('jquery-flash-init',get_template_directory_uri().'/js/flash.js',array('jquery','jquery-flash'),'1.0',true);
	wp_register_script('scrollTo',get_template_directory_uri().'/js/jquery.scrollTo-1.4.2-min.js',array('jquery'),'1.4.2',true);
	wp_register_script('localscroll',get_template_directory_uri().'/js/jquery.localscroll-1.2.7-min.js',array('jquery','scrollTo'),'1.2.7',true);
	wp_register_script('scroll-init',get_template_directory_uri().'/js/scroll.js',array('jquery','scrollTo','localscroll'),'1.0',true);
	wp_register_script('social-init',get_template_directory_uri().'/js/socials.js',array('jquery'),'1.0',true);
	wp_register_script('management-init',get_template_directory_uri().'/js/management.js',array('jquery','cycle'),'1.0',true);
	
	//wp_enqueue_script('app');
	wp_register_style('fancybox', get_template_directory_uri().'/css/jquery.fancybox-1.3.4.css', null, '1.3.4', 'screen');
	wp_register_style('main', get_template_directory_uri().'/css/main.css', null, '1.0', 'screen');
	//TODO: split the css and include only if needed.
	
	
	
	
	//wp_enqueue_script('app');
	wp_enqueue_style('main');
}


/**
 * This is experimental code :)
 * w3 total cache hook to enable symlinked files to be minified.
 */
add_filter('w3tc_custom_minapp_options', 'allow_dirs', 10, 1);
function allow_dirs($arr){
	$arr['allowDirs'] = array_merge((array)$arr['allowDirs'], array('/usr/local/fastbooking/'));
	return $arr;
}

//add_filter('post_thumbnail_html', array('theme_helpers', 'image_preloader'), 10, 5);


add_filter('page_link', array('theme_helpers', 'fix_links'), 10, 3);


/**
 * PECT_HTTP is missing...
 * @param array $parsed_url the array to be merged
 * @return string the url
 */
if(!function_exists('http_build_url')){
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
register_nav_menu('primary', __('Primary Menu', theme_helpers::textdomain));
register_nav_menu('secondary', __('Secondary Menu', theme_helpers::textdomain));

register_sidebar(array(
		'name' 			=>	__('Home'),
		'id' 			=>	'home-sidebar',
		'description' 	=>	__('Widgets in this area will be shown on the right-hand side of the Homepage.'),
		'before_title' 	=>	'<h2>',
		'after_title' 	=>	'</h2>',
		'before_widget'	=>	'',
		'after_widget'	=>	'',
));

add_filter('show_admin_bar', '__return_false');
add_filter('body_class', array('theme_helpers', 'body_class'));
add_action('wp_head', array('theme_helpers', 'header'), -999);
add_shortcode('pdf-or-contact-cta', array('theme_helpers', 'pdf_or_contact_cta'));


add_theme_support('post-thumbnails');
add_image_size('slideshow-image-left', 500, 320, false);
add_image_size('slideshow-image-background', 1900, 600, false);

add_image_size('second-level-thumb-1', 340, 210, true);
add_image_size('second-level-thumb-2', 340, 120, true);
add_image_size('second-level-thumb-3', 220, 120, true);

add_image_size('third-level-thumb', 400, 220, true);
add_image_size('fourth-level-thumb', 340, 120, true);

add_image_size('key-figure', 690, 330, true);

//add_filter('image_downsize', array('theme_helpers', 'image_resizer'), 10, 3); //false, $id, $size);

add_action('wp_ajax_download', array('theme_helpers', 'force_download'));
add_action('wp_ajax_nopriv_download', array('theme_helpers', 'force_download'));

/**
 * Remove some useless css and js by wpml
 */
define(ICL_DONT_LOAD_LANGUAGES_JS, true);
define(ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS, true);
define(ICL_DONT_LOAD_NAVIGATION_CSS, true);


/**
 * Some usefull static methods for the theme
 * @author etessore
 * @version 1.0.0
 */
class theme_helpers{
	/**
	 * @var boolean true if the debug is enabled, false otherwise
	 */
	const debug = true;
	/**
	 * @var string Stores the name of the gettext domain for this theme
	 */
	const textdomain = 'theme';
	
	/**
	 * @var string the author
	 */
	const author = 'Emanuele \'Tex\' Tessore';
	
	/**
	 * Lock variable: enables or disables the filter
	 * to get the permalink with hash or not
	 * @var boolean
	 */
	static $use_hash_permalink = true;
	
	/**
	 * This filter callback adds some usefull classes to the body
	 * Remember to call body_class() in the theme!
	 * 
	 * @param array $classes the classes already added by wordpress or some other plugin
	 */
	public static function body_class($classes){
		$tags = fb_get_systags(get_the_ID(), false);
		foreach($tags as $k => $tag){
			$tags[$k] = 'tag-'.$tag;
		}
		$classes = array_merge($classes, $tags);
		if(is_super_admin()) $classes[] = 'super-admin';
		if(!is_user_logged_in()) $classes[] = 'logged-out';
		
		return $classes;
	}
	
	/**
	 * @return some usefull meta tags for the <head>
	 */
	public static function header(){
		$tempate_directory_uri = get_template_directory_uri();
		$charset = get_bloginfo('charset');
		$author = self::author;
		if(function_exists('fbseo_get_title')){
			$title = fbseo_get_title();
			$description = fbseo_get_metadescription();
		} else {
			
			$wpseo = new WPSEO_Frontend();
			//$title = $wpseo->title();
			$description = $wpseo->metadesc(false);
			$title = wp_title('', false);
			//$description = WPSEO_Frontend::metadesc(false);
		}
		
		
		echo <<<EOF
	<title>$title</title>
	<meta name="description" content="$description" />
    <meta charset="$charset">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="$author">
    <link rel="shortcut icon" href="$tempate_directory_uri/images/favicon.png">
EOF;
		if(false && self::debug){
			echo <<<EOF

	<style>
		.debug{margin:10px;border:1px solid red;font-size:150%;}
	</style>
EOF;
		}
	}
	
	/**
	 * Get the markup for the secondary navigation menu
	 * on the header part of the page
	 * @param array $foced_posts the list of the hash to be printed
	 */
	public static function header_sec_nav($forced=null){
		$menu_elements = array();
		$toret = '';
		
		if(is_null($forced)){
			$posts = fb_get_all_childrens_of();
			if(count($posts)){
				foreach($posts as $p){
					$tmp = array();
					$tmp['href'] = theme_helpers::post_hash($p);
					$tmp['title'] = get_the_title($p->ID);
					$menu_elements[] = $tmp;//theme_helpers::post_hash($p);
				}
			}
		} else {
			$menu_elements = $forced;
		}
		
		$menu_elements = apply_filters('header-sec-nav', $menu_elements);
		if(count($menu_elements)){
			$toret .= '<div id="sec-nav"><ul>';
			global $post;
			wp_enqueue_script('scroll-init');
			$tpl = '<li class="first-level-menu localscroll">%s</li>';
			foreach($menu_elements as $menu_element){
				$toret .= sprintf (
					$tpl,
					theme_helpers::anchor(
						'#'.sanitize_title($menu_element['href']),
						esc_html($menu_element['title']),
						'class="nav-bull sprite"'
					)
				);
			}
			wp_reset_postdata();
			$toret .= '</ul></div>';
		}
		
		return $toret;
	}
	
	/**
	 * Gets the correct title for the current page
	 * @param string $forced the title to force
	 */
	public static function header_page_title($forced=null){
		if(is_404()) return __('Page Not Found', self::textdomain);
		if(is_search()) return __('Search', self::textdomain); 
		if($forced) return $forced;
		return get_the_title();
	}
	
	/**
	 * Retrieves the markup for the -init hash placeholder
	 * @param unknown_type $hash
	 */
	public static function hash_placeholder($hash=null){
		wp_enqueue_script('scroll-init');
		if(is_null($hash)) {
			$hash = self::post_hash(null, false);
		}
		$hash = sanitize_title($hash);
		return <<< EOF
			<div id="$hash-init" class="hash-placeholder"></div>		
EOF;
	}
	
	/**
	 * TODO: there's no hook for every image, only for the post thumb
	 * The idea is to remove every <img> tag and substitute it with
	 * a js array to images.
	 * The second step is to merge all these images into one 
	 * and serve them with a sprite.
	 * @param unknown_type $html
	 * @param unknown_type $post_id
	 * @param unknown_type $post_thumbnail_id
	 * @param unknown_type $size
	 * @param unknown_type $attr
	 */
	public static function image_preloader($html, $post_id, $post_thumbnail_id, $size, $attr){
		$img = array(
			'src'		=>	'',
			'alt'		=>	'',
			'class'		=>	'',
			'width'		=>	'',
			'height'	=>	''
		);
		$toRet = <<< EOF
			<script>
				images_to_be_loaded = [] || images_to_be_loaded;
				images_to_be_loaded.push();
			</script>
			<span class="image-preload" id="image-$post_thumbnail_id">
EOF;
		return $html;
	}
	

	/**
	 * Manages the url rewritting for the theme
	 * @param unknown_type $permalink
	 * @param unknown_type $post_id
	 * @param unknown_type $leavename
	 */
	public static function fix_links($permalink, $post_id, $leavename){
		if(!self::$use_hash_permalink) return $permalink;
		
		$current_post = get_queried_object();
		
		self::$use_hash_permalink = false;
		
		if(is_object($post_id)){
			$post = $post_id;
		} else {
			$post = get_post($post_id);
		}
		
		if($post->post_parent != 0 && fb_has_systag('anchor-hash', $post->post_parent)){
			if(! fb_has_systag('anchor-hash', $post->ID)){
				if($post->post_parent == $current_post->ID){
					$permalink = '#'.ltrim(self::post_hash($post), '#');
				} else {
					$permalink = get_permalink($post->post_parent).'#'.ltrim(self::post_hash($post), '#');
				}
			}
		}

		self::$use_hash_permalink = true;
		
		return $permalink;
	}
	
	/**
	 * @return the markup for a youtube video anchor
	 * @param string $id the youtube video id
	 */
	public static function youtube_video($id){
		$label = __('Whatch this video on You Tube!',self::textdomain);
		return <<< EOF
			<a rel="nofollow" href="http://www.youtube.com/v/$id?version=3&enablejsapi=1&playerapiid=$id>">$label</a>
EOF;
	}
	
	/**
	 * Retrieves the markup for the go to top button
	 * If $k is 0 returns an empty string
	 * @param int $k the counter, generally a foreach($arr as $k=>$v)
	 */
	public static function go_to_top($k=null){
		if(is_null($k)){ global $k; }
		if(!$k) return '';
		return '<div class="top sprite"></div>';
	}

	/**
	 * @return the markup for the 404 menu
	 */
	public static function get_404_escape_route(){
		$tokens = array();
		$tokens['back'] 		= self::anchor('javascript:history.back();', __('Back', self::textdomain));
		$tokens['home']			=	self::anchor(get_home_url(), __('Home', self::textdomain));
		$tokens['contact']	=	self::anchor(get_permalink(get_page_by_title('Contacts')->ID), __('Contacts', self::textdomain));
		return __('Maybe those links will be usefull: ', self::textdomain).'<br>'.implode(' - ', $tokens);
	}

	/**
	 * Util to debug some variable when you are going mad 
	 * about something you can't understand withou knowing the value of it.
	 * 
	 * @param mixed $var the variable to be exported
	 * @param string $title the title or keywork: usefull if you hit ctrl+f on the source code :)
	 * @param boolean $echo true if you want to echo the variable value
	 * @param boolean $comment true if you want to wrap the echo on an html comment: very usefull in production mode
	 * @param boolean $die true if you want to stop execution after dumping the variable.
	 */
	public static function debug($var, $title='DEBUG', $echo=true, $comment=true, $die=false){
		$tpl = <<<EOF
		<div class="debug" style="margin:10px;border:1px solid red;font-size:150%;">
			<h1>%title%</h1>
			<pre>%debug%</pre>
		</div>
EOF;
		$render = str_replace(array('%debug%','%title%'), array(var_export($var, true), $title), $tpl);
		if($comment) $render = '<!-- '.$render.' -->';
		if($die) die($render);
		if($echo) echo $render;
		return $render;
	}
	
	/**
	 * Limits the string lenght to the given param
	 * @return the limited string
	 * @param string $string the string to be limited
	 * @param int $word_limit the number of words
	 * @param string $suffix the suffix to append at the end of the limited string, default is '' (none)
	 */
	public static function limit_words($string, $word_limit, $suffix=''){
		$words = explode(' ', $string, ($word_limit + 1));
		if(count($words) > $word_limit)
			array_pop($words);
		$s = implode(' ', $words);
		return (strlen($string) == strlen($s)) ? $string : $s.$suffix;
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
	 * Provides timthumb as image resizer
	 * @param boolean $is_intermediate
	 * @param int $id the attachment id
	 * @param string $size size as recorded with add_image_size
	 */
	public static function image_resizer($is_intermediate, $id, $size){
		global $_wp_additional_image_sizes;
		if(!in_array($size, array_keys($_wp_additional_image_sizes)) || !$_wp_additional_image_sizes[$size]['crop'])
			return false;
		
		$img_url = wp_get_attachment_url($id);
		$width = $_wp_additional_image_sizes[$size]['width'];
		$height = $_wp_additional_image_sizes[$size]['height'];
		$img_url .= '?w='.$width.'&h='.$height.'&q=50';

		return array( $img_url, $width, $height, $is_intermediate );
	}
	
	/**
	 * Test if the post has a thumbnail
	 * @param object|int $post the post to be checked
	 */
	public static function has_thumbnail($post=null){
		if(is_null($post)){
			global $post;
			$post_id = $post->ID;
		} elseif(is_numeric($post)){
			$post_id = $post;
		} else {
			$post_id = $post->ID;
		}
		$thumb = get_post_thumbnail_id($post_id);
		return !empty($thumb);
	}
	
	/**
	 * Test if the given $post has the $code in its content
	 * @return boolean true if the code is in the post content
	 * @param string $code the code to be checked
	 * @param object $post the post to search in
	 */
	public static function has_shortcode($code, $post=null){
		if(is_null($post)) global $post;
		return (bool) stripos($post->post_content, '['.$code.']');
	}
	
	/**
	 * Get the 'Contact Us' cta markup
	 * @param string|array $parms @see theme_helpers::anchor()
	 */
	public static function contact_us($parms='class="cta sprite"', $label=null){
		if(is_null($label)){ $label = __('Contact Us Now!', self::textdomain); }
		$contact_us_post_id = get_page_by_title('Contacts'); //56;
		$toret = self::anchor(
			get_permalink($contact_us_post_id), 
			$label,
			$parms
		);
		return $toret;
		//TODO: contact popup?
		$toret .= <<< EOF
			<div class="contact-us-popup">
			
			</div>
EOF;
	}
	
	/**
	 * Return the page link to the given $post.
	 * If $post is not child of the current page
	 * a '-init' will be appended so that you 
	 * can css the div#postslug-init to avoid
	 * position fixed elements.
	 * @param object $post the post to be ashed
	 */
	public static function post_hash($post=null, $add_init=true){
		if(is_null($post)){
			global $post;
		}
		if($add_init && (get_queried_object_id() != $post->post_parent)){
			return trim(basename(get_permalink($post->ID))."-init", '#');
		}
		return trim(str_replace('-init', '', basename(get_permalink($post->ID))), '#');
	}
	
	/**
	 * @return the markup for an html <a> tag
	 * @param string $href the url to be pointed
	 * @param string $label the text
	 * @param array|string $parms some html attributes in key=>value pairs or a plain string
	 */
	public static function anchor($href, $label, $parms=''){
		$href 	= esc_attr($href);
		//$label 	= esc_html($label); //i want the possibility to insert an inner <img>
		$parms 	= trim(self::array_to_html_attributes('=', $parms));
		if(!empty($parms)) $parms = ' '.$parms;
		return <<< EOF
		<a href="$href"$parms>$label</a>
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
	 * Retrieves the cta for download the product sheet
	 * by searching in the attached files for a pdf
	 * If none is found the returns a link to the contact page
	 * @param int|object $post the post
	 */
	public static function pdf_or_contact_cta($post=null, $label=null){
		if(is_null($post)) { global $post; }
		if(is_null($label)) { $label = __('Download Product Sheet', self::textdomain); }
		$post = get_post($post);
		setup_postdata($post);	
		$toret = '';
		$pdfs = get_posts(array(
			'post_type' 		=>	'attachment',
			'numberposts' 		=>	null,
			'post_status' 		=>	'published',
			'post_parent' 		=>	get_the_ID(),
			'post_mime_type' 	=>	'application/pdf'
		));
		if(count($pdfs)){
			foreach($pdfs as $pdf){
				$toret .= self::force_download_anchor(
					$pdf->ID,
					$label.'<span class="sprite cta-download"></span>',
					'class="cta sprite relative"'
				);
			}
		} else {
			$toret .= self::contact_us('class="sprite cta-2"');
		}
		return '<div class="buttons">'.$toret.'</div>';
	}
	
	/**
	 * AJAX callback for forcing the download of a file
	 */
	public static function force_download(){
		if(!is_numeric($_GET['id'])){
			wp_die('Unauthorized Resource');
		}
		$file = get_attached_file($_GET['id']);
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename= " . basename($file));
		header("Content-Transfer-Encoding: binary");
		die(file_get_contents($file));
	}
	
	/**
	 * 
	 * @param int $id
	 * @param string $label
	 * @param array|string $parms @see 
	 */
	public static function force_download_anchor($id, $label, $parms){
		return self::anchor(admin_url('admin-ajax.php').'?action=download&id='.$id, $label, $parms);
	}
	
	/**
	 * If $value is numeric returns the permalink to the post with $value id.
	 * If not returns $value. 
	 * @param string $value
	 */
	public static function get_cta_href($value){
		if(is_numeric($value)){
			return get_permalink($value);
		}
		return $value;
	}
	
	/**
	 * Return a label for the CTA button.
	 * @param string $value
	 */
	public static function get_cta_text($value=null){
		if(isset($value) && !empty($value)){
			return $value;
		}
		return __('More Info', self::textdomain);
	}
	
	/**
	 * Get the markup for the autosize text
	 * @param string $data the text to be marked up
	 * @param string $tpl sprintf formatted string
	 */
	public static function get_autosize_text($data=null, $tpl=null){
		wp_enqueue_script('text-autosize');
		if(is_null($data)){
			$data = trim(
				simple_fields_get_post_value(
					get_the_id(),
					"Title HTML",
					true
				)
			);
		}
		if(!empty($data)){
			$tokens = explode("\n", $data);
		} else {
			$tokens = explode(" ", trim(get_the_title()));
		}
		if(is_null($tpl)){
			$tpl = '<span class="text-autosize">%s</span>';
		}
		$label = '';
		foreach($tokens as $token){
			$label .= sprintf($tpl, trim($token));
		}
		return $label;
	}
	

}

/**
 * Quick and dirty way to know a variable value
 * vd stand for <strong>v</strong>ar_dump() and <strong>d</storng>ie()
 * @param mixed $var the variable to be dumped
 */
function vd($var){
	theme_helpers::debug($var, 'DEBUG', true, false, true);
}

/**
 * Quick and dirty way to know a variable value
 * Usefull in a loop cause it doesn't break the execution with die
 * @param mixed $var the variable to be dumped
 */
function v($var){
	theme_helpers::debug($var, 'DEBUG', true, false, false);
}

/**
 * Adds the level for the current menu element
 * @author etessore
 *
 */
class Level_Walker_Nav_Menu extends Walker_Nav_Menu {
	public function start_el(&$output, $item, $depth, $args){
		$item->classes[] = 'level-'.$depth;
		//$item->classes[] = ($item->menu_order % 2 != 0) ? 'odd'.$item->menu_order : 'even'.$item->menu_order;
		return parent::start_el(&$output, $item, $depth, $args);
	}
}


/**
 * Parent class to be extended to setup the custom feature
 * @author etessore
 */
abstract class Custom_Feature{
	const textdomain = theme_helpers::textdomain;
	var $custom_post_labels;
	var $hooks;
	var $custom_taxonomy_labels;
	var $subtitle_shortcut;
	var $has_archives = true;
	
	/**
	 * Sets the custom post labels for this feature
	 * @param array $atts the labels
	 * @return Custom_Feature $this for chainability
	 */
	public function set_custom_post($atts){
		$this->custom_post_labels = $atts;
		if(count($this->custom_post_labels['fields'])){
			foreach($this->custom_post_labels['fields'] as $k => $v){
				$this->custom_post_labels['fields'][$k]['id'] = $k;
			}
		}
		return $this;
	}
	
	/**
	 * Sets the custom taxonomy labels for this feature
	 * @param array $atts the labels
	 * @return Custom_Feature $this for chainability
	 */
	public function set_custom_taxonomy($atts){
		$this->custom_taxonomy_labels = $atts;
		return $this;
	}
	
	/**
	 * Retrieves the entries for the custom taxonomy
	 */
	public function get_taxonomy_entries(){
		return get_terms($this->custom_taxonomy_labels['slug']);
	}
	
	/**
	 * Retrieves the markup for the view all our clients cta.
	 */
	public function full_list_cta(){
		return '<div class="full-list-cta">'.theme_helpers::anchor(
			get_post_type_archive_link($this->custom_post_labels['slug']), 
			__('View all', self::textdomain),
			'class="sprite cta"'
		).'</div>';
	}
	
	/**
	 * Retrieves the markup for the zone list
	 */
	public function taxonomy_entries_list(){
		$zones = $this->get_taxonomy_entries();
		$toret = '';
		if(count($zones)){
			$toret .= '<ul class="list-zones list-'.$this->custom_taxonomy_labels['slug'].'">';
			foreach($zones as $zone){
				$toret .= '<li>';
				$toret .= theme_helpers::anchor(get_term_link($zone), $zone->name);
				$toret .= '</li>';
			}
			$toret .= '</ul>';
		}
		echo $toret;
	}
	
	/**
	 * Enable the subtitle shorcut feature
	 * @param string $shortcut the shortcut that manages the subtitle
	 * @return Custom_Feature $this for chainability
	 */
	public function set_subtitle_shortcut($shortcut){
		$this->subtitle_shortcut = $shortcut;
		return $this;
	}
	
	/**
	 * Enable the rewriting of the custom post
	 * permalink with the post slug as url hash
	 * @return Custom_Feature $this for chainability
	 */
	public function set_post_permalink_with_hash(){
		add_filter(
			'post_type_link',
			array($this, 'custom_post_permalink'),
			10,
			3
		);
		return $this;
	}
	
	/**
	 * Enable the rewriting of the custom taxonomy
	 * permalink with the tax slug as url hash
	 * @return Custom_Feature $this for chainability
	 */
	public function set_taxonomy_permalink_with_hash(){
		add_filter(
			'term_link', 
			array($this, 'custom_taxonomy_permalink'), 
			10, 
			3
		);
		return $this;
	}
	
	/**
	 * Hooks the get_taxonomy_entries_list() method to the
	 * single-entry-title-<post-hash> action to show
	 * the list under the section title
	 * @param object $post the post to be checked
	 */
	public function subtitle_manager($post){
		if(theme_helpers::has_shortcode($this->subtitle_shortcut, $post)){
			add_filter(
				'single-entry-title-'.theme_helpers::post_hash($post), 
				array(&$this, 'taxonomy_entries_list')
			);
		}
	}
	
	/**
	 * Sets the hooks for this feature
	 * @param array $atts the labels
	 * @return Custom_Feature $this for chainability
	 */
	public function set_hooks($atts){
		$this->hooks = $atts;
		return $this;
	}
	
	/**
	 * Set the feature up and running:
	 * register custom posts and hooks
	 * @return Custom_Feature $this for chainability
	 */
	public function setup(){
		return $this->register_custom_post()
			->set_taxonomy_permalink_with_hash()
			->set_post_permalink_with_hash()
			->register_custom_taxonomy()
			->hook();
	}
	
	/**
	 * Register hooks for this feature
	 * @return Custom_Feature $this for chainability
	 */
	public function hook(){
		if(count($this->hooks['actions'])){
			foreach($this->hooks['actions'] as $tag => $function){
				add_action($tag, $function);
			}
		}
		if(count($this->hooks['shortcodes'])){
			foreach($this->hooks['shortcodes'] as $tag => $function){
				add_shortcode($tag, $function);
			}
		}
		if(!is_admin()){
			add_filter(
				'wp_get_nav_menu_items',
				array($this, 'navmenu'), 
				10, 
				3
			);
			if(!empty($this->subtitle_shortcut)){
				add_filter(
					'the_post',
					array($this, 'subtitle_manager'),
					10,
					1
				);
				add_shortcode($this->subtitle_shortcut, __return_false);
			}
		}
		
		return $this;
	}
	
	/**
	 * Change the nav menu element with url %last-<slug-of-the-feature>%
	 * with a list of last 5 entries for this feature
	 * @param array $items list of elements
	 * @param array $menu the menu
	 * @param array $args optional args
	 */
	public function navmenu($items, $menu, $args){
		$menu_order = 0;
		$toret = array();
		foreach($items as $k => $item){
			if(strpos($item->url, '%last-'.$this->custom_post_labels['slug'].'%')!==false){
				$posts = $this->get_posts();
				foreach($posts as $post){
					$post->menu_item_parent = $item->menu_item_parent;
					$post->post_type = 'nav_menu_item';
					$post->object = 'custom';
					$post->type = 'custom';
					$post->menu_order = ++$menu_order;
					$post->title = $post->post_title;
					$post->url = get_permalink( $post->ID );
					/* add as a child */
					$toret[] = $post;
				}
				/*$more = $items[$k-1];
				$more->menu_item_parent = $item->menu_item_parent;
				$more->title = __('More', self::textdomain);
				$more->menu_order = ++$menu_order;
				$more->type = 'custom';
				$more->object = 'custom';
				//$toret[] = $more;/**/
			} elseif(strpos($item->url, '%list-'.$this->custom_taxonomy_labels['slug'].'%')!==false) {
				$posts = $this->get_taxonomy_entries();
				foreach($posts as $post){
					$post->menu_item_parent = $item->menu_item_parent;
					$post->post_type = 'nav_menu_item';
					$post->object = 'custom';
					$post->type = 'custom';
					$post->menu_order = ++$menu_order;
					$post->title = $post->name;
					$post->url = get_term_link( intval($post->term_id), $this->custom_taxonomy_labels['slug'] );
					/* add as a child */
					$toret[] = $post;
						
				}
			} else {
				$item->menu_order = ++$menu_order;
				$toret[] = $item;
				
			}
		}
		return $toret;
	}
	
	/**
	 * Retrives some posts for the current feature
	 * @see http://codex.wordpress.org/Template_Tags/get_posts
	 * @param array $args get_posts $args
	 */
	public function get_posts($args=array()){
		$defaults = array(
			'numberposts'	=>	5,
			'post_status'	=>	'publish',
			'orderby'		=>	'menu_order'
		);
		$args = array_merge($defaults, $args);
		$args['post_type'] = $this->custom_post_labels['slug'];
		return get_posts($args);
	}
	
	/**
	 * Register the custom posts for this feature
	 * @return Custom_Feature $this for chainability
	 */
	public function register_custom_post(){
		if(count($this->custom_post_labels)==0) { return $this; }
		register_post_type(
			$this->custom_post_labels['slug'], 
			array(
				'label' => $this->custom_post_labels['label'],
				'description' => $this->custom_post_labels['description'],
				'public' => true,
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_nav_menus' => true,
				'show_in_menu' => true,
				'show_in_admin_bar' => true,
				'menu_position'	=> null,
				'menu_icon' => null,
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => false,
				'rewrite' => array('slug' => $this->custom_post_labels['slug']),
				'query_var' => true,
				//'has_archive' => $this->has_archives,
				'supports' => array(
					'title',
					'editor',
					'excerpt',
					'trackbacks',
					'custom-fields',
					'comments',
					'revisions',
					'thumbnail',
					'author',
					'page-attributes',
				),
				'labels' => array (
					'name' => $this->custom_post_labels['label'],
					'singular_name' => $this->custom_post_labels['sing-label'],
					'menu_name' => $this->custom_post_labels['label'],
					'add_new' => 'Add '.$this->custom_post_labels['sing-label'],
					'add_new_item' => 'Add New '.$this->custom_post_labels['sing-label'],
					'edit' => 'Edit',
					'edit_item' => 'Edit '.$this->custom_post_labels['sing-label'],
					'new_item' => 'New '.$this->custom_post_labels['sing-label'],
					'view' => 'View '.$this->custom_post_labels['sing-label'],
					'view_item' => 'View '.$this->custom_post_labels['sing-label'],
					'search_items' => 'Search '.$this->custom_post_labels['label'],
					'not_found' => 'No '.$this->custom_post_labels['label'].' Found',
					'not_found_in_trash' => 'No Clients Found in Trash',
					'parent' => 'Parent '.$this->custom_post_labels['sing-label'],
				)
			)
		);
		return $this;
	}
	
	/**
	 * Register the taxonomy for this feature
	 * @return Custom_Feature $this for chainability
	 */
	public function register_custom_taxonomy(){
		if(count($this->custom_taxonomy_labels)){
			register_taxonomy(
				$this->custom_taxonomy_labels['slug'],
				array(0 => $this->custom_post_labels['slug']),
				array(
					'hierarchical' => true,
					'label' => $this->custom_taxonomy_labels['label'],
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => true,
					'singular_label' => $this->custom_taxonomy_labels['sing-label']
				)
			);
		}
		return $this;
	}
	
	/**
	 * Retrieves the markup for the horizontal scroller
	 * @param array $posts the list of elements, default an empty array
	 * @param int $entries_per_slide the numeber of elements in each slide, default 3
	 * @param string $class the class wrapper, default 'portfolio'
	 * @param string $expandable is this to be expandable or not?
	 */
	public function horizontal_scroll($posts = array(), $entries_per_slide = 3, $class = 'expandable-details', $expandable=true){
		$toret = '';
		if($expandable) $expandable = ' expandable';
		$count = count($posts);
		if($count){
			global $post;
			$toret .= '<div class="'.$class.'">';
			$toret .= $this->get_top_of_the_slide();
			$toret .= '<div class="relative"><div class="cycle'.$expandable.'" data-fx="scrollHorz"><div class="cycle-element">';
			foreach($posts as $k => $post){
				setup_postdata($post);
				if($k && $k%$entries_per_slide==0) $toret .= '</div><div class="cycle-element">';
				$toret .= $this->horizontal_scroll_element();
			}
			$toret .= '</div></div>';
			if($count > $entries_per_slide) { $toret .= $this->get_prev_next(); }
			$toret .= $this->get_bottom_of_the_slide();
			$toret .= '</div></div>';
			wp_reset_postdata();
		}
		return $toret;
	}
	
	/**
	 * Retrieves the markup for the list
	 * of elements in a horizontal_scroll
	 * @param array $posts the list of posts
	 */
	public function horizontal_scroll_element(){
		$title		=	get_the_title();
		$excerpt	=	get_the_excerpt();
		$content	=	apply_filters('the_content', get_the_content());
		$read_more	=	__('Read More', self::textdomain);
		
		return <<< EOF
			<div class="single-element relative">
				<h3 class="title second-level">
					$title
				</h3>
				<div class="body">
					$excerpt
				</div>
				<div class="more">
					<a class="sprite cta-2" href="javascript:;">$read_more</a>
				</div>
				<div class="description relative">
					<div class="title">$title</div>
					$content
					<div class="close-description sprite close"></div>
				</div>
			</div>
EOF;
	}
	
	/**
	 * Get the markup for a thumbnail
	 * @param object|int $post the post
	 * @param string $size the size of the tumbnail
	 * @return string html markup
	 */
	public function thumbnail($post=null, $size='portfolio-thumb-3'){
		if(is_null($post)){ global $post; }
		if(is_numeric($post)){ $post = get_post($post); }
		$post_meta = trim(get_post_meta( $post->ID, 'urldesc', true ));
		$desc = (empty($post_meta)) 
			? get_the_title($post->ID)
			: $post_meta;
		return get_the_post_thumbnail(
			$post->ID, 
			$size
		);
	}
	
	/**
	 * Get the markup for a single thumbnail with the overlay
	 * If url description is empty the overlay 
	 * will be the title of the clients custom post
	 * @param object|int $post the post
	 * @param string $size the size of the tumbnail
	 * @return string html markup
	 */
	public function thumbnail_with_overlay($post=null, $size='portfolio-thumb-3'){
		if(is_null($post)){ global $post; }
		if(is_numeric($post)){ $post = get_post($post); }
		$desc = (get_post_meta( $post->ID, $this->thumbnail_metas['overlay'], true ));
		if(is_null($this->thumbnail_metas['overlay']) || empty($desc)){
			$desc = get_the_title($post->ID);
		}
		$anchor = get_post_meta( $post->ID, $this->thumbnail_metas['anchor'], true );
		if(is_null($this->thumbnail_metas['overlay']) || empty($anchor)){
	 		return 
	 			'<span id="'.sanitize_title($post->post_title) .'" '.
				'class="'.$this->custom_post_labels['slug'].'-thumb thumb-with-overlay relative">'.
				$this->thumbnail(
					$post->ID, 
					$size
				).'<span class="overlay">'.$desc.'</span>'.
				'</span>'
			;
		} else {
	 		return theme_helpers::anchor(
				esc_attr($anchor),
				$this->thumbnail(
					$post->ID, 
					$size
				).'<span class="overlay">'.$desc.'</span>',
				array(
					'id'	=>	sanitize_title($post->post_title),
					'class'	=>	$this->custom_post_labels['slug'].'-thumb thumb-with-overlay relative',
					'rel'	=>	'nofollow',
					'target'=>	'_blank'
				)
			);
		}
	}
	
	/**
	 * Get the markup for a single thumbnail with the lightbox feature
	 * If url description is empty the overlay
	 * will be the title of the clients custom post
	 * @param object|int $post the post
	 * @param string $size the size of the tumbnail
	 * @return string html markup
	 */
	public function thumbnail_with_lightbox($post=null, $size='portfolio-thumb-3'){
		if(is_null($post)){ global $post; }
		if(is_numeric($post)){ $post = get_post($post); }
		wp_enqueue_script('fancybox-init');
		wp_enqueue_style('fancybox');
		$desc = (get_post_meta( $post->ID, $this->thumbnail_metas['overlay'], true ));
		if(empty($desc)){
			$desc = get_the_title($post->ID);
		}
		$big_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
		return theme_helpers::anchor(
			$big_img[0],
			$this->thumbnail(
				$post->ID, 
				$size
			).'<span class="overlay">'.$desc.'</span>',
			array(
				'id'			=>	sanitize_title($post->post_title),
				'class'			=>	$this->custom_post_labels['slug'].'-thumb thumb-with-overlay fancybox relative',
				'rel'			=>	$this->custom_post_labels['slug'].'-group',
				'data-url'		=>	esc_attr(get_post_meta( $post->ID, $this->thumbnail_metas['anchor'], true )),
				'data-label'	=>	esc_attr(get_post_meta( $post->ID, $this->thumbnail_metas['overlay'], true ))
			)
		);
	}
	
	/**
	 * Retrieves the markup for the box with image and cta button
	 * TODO: replace the get_job_name method!!!
	 */
	public function thumbnail_with_cta($post=null, $size='portfolio-thumb-3'){
		wp_enqueue_script('management-init');
		if(is_null($post)) { global $post; }
		if(is_numeric($post)){ 
			$p = get_post($post); 
			global $post; 
			$post = $p; 
		}
		setup_postdata($post);
		$toret = 
			'<div class="manager single-element">'
			.$this->thumbnail_with_overlay($post, $size)
			//.'<div class="name">'.$desc.'</div>'
			.'<div class="job-name">'.$this->get_job_name($post->ID).'</div>'
			.'<div class="more"><a class="sprite cta" href="javascript:;">'.__('Read More', self::textdomain).'</a></div>'
			.'<div class="description">'.'<div class="title">'.get_the_title().'</div>'.apply_filters('the_content', get_the_content()).'</div>'
			.'</div>';
		wp_reset_postdata();
		return $toret;
	}
	
	/**
	 * Get the markup for Prev and Next buttons
	 */
	public function get_prev_next(){
		wp_enqueue_script('cycle-init');
		return '<div class="prev sprite"></div><div class="next sprite"></div>';
	}
	
	/**
	 * Get the markup for the slide open description 
	 * at the bottom of an horizontal_scroll element
	 */
	public function get_bottom_of_the_slide(){
		return <<< EOF
			<div class="relative slide-margin-fixer">
				<div class="open-description"></div>
				<div class="close-deco">&nbsp;</div>
				<div class="close-description sprite close"></div>
				<div class="pager"></div>
			</div>
EOF;
	}
	
	/**
	 * Get the markup for the slide open description 
	 * at the top of an horizontal_scroll element
	 */
	public function get_top_of_the_slide(){
		
	}
	
	/**
	 * Fixes permalinks for this feature custom post
	 * @param string $permalink the wp permalink
	 * @param object $post_id
	 * @param bool $leavename
	 * @return string the correct permalink
	 */
	public function custom_post_permalink($permalink, $post_id, $leavename){
		
		if($post_id->post_type == $this->custom_post_labels['slug']){
			
			$parsed = parse_url($permalink);
			
			//rtrim because with hash the trailing slash is not correct
			$path = explode('/', rtrim($parsed['path'], '/'));
			$parsed['fragment'] = array_pop(&$path).'-init';
			$parsed['path'] = implode('/', $path);
			
			$permalink = 
				(is_post_type_archive($this->custom_post_labels['slug'])) 
				? '#'.$parsed['fragment'] 
				: http_build_url($parsed);
		}
		return $permalink;
	}
	
	/**
	 * Fixes the permalinks for the zones custom taxonomy
	 * @param string $termlink the permalink
	 * @param object $term the taxonomy
	 * @param string $taxonomy the taxonomy slug
	 */
	public function custom_taxonomy_permalink($termlink, $term, $taxonomy){
		if($taxonomy==$this->custom_taxonomy_labels['slug']){
			$parsed = parse_url($termlink);
			$parsed['path'] = str_replace(
				$this->custom_taxonomy_labels['slug'], 
				$this->custom_post_labels['slug'], 
				$parsed['path']
			);
			
			//rtrim because with hash the trailing slash is not correct
			$path = explode('/', rtrim($parsed['path'], '/'));
			$parsed['fragment'] = array_pop(&$path).'-init';
			$parsed['path'] = implode('/', $path);
			
			$termlink = 
				(is_post_type_archive($this->custom_post_labels['slug'])) 
				? '#'.$parsed['fragment'] 
				: http_build_url($parsed);
		}
		return $termlink;
	}
	
	/**
	 * Disable archives for this custom feature
	 * @return Custom_Feature $this for chainability
	 */
	public function disable_archives(){
		$this->has_archives = false;
		return $this;
	}
}

/**
 * Describes a custom feature with link metabox.
 * @author etessore
 */
abstract class Custom_Feature_With_Metaboxes extends Custom_Feature{
	var $custom_box;
	var $thumbnail_metas;
	
	/**
	 * Set the custom box for this feature
	 * @param array $atts the attributes
	 * @return Custom_Feature_With_Metaboxes $this for chainability
	 */
	public function set_custom_box($atts=array()){
		$custom_box_defaults = array(
			'id'		=>	'custom-box-id',
			'title'		=>	__('Fastbooking Custom Box', self::textdomain),
			'fields'	=>	array(
				'siteurl' 	=>	array(
					'label'		=>	__('Url:', self::textdomain),
					'value'		=>	'http://'
				),
				'urldesc'	=>	array(
					'label'		=>	__('Description:', self::textdomain),
					'value'		=>	''
				)
			)
		);
		$this->custom_box = array_merge($custom_box_defaults, $atts);
		foreach($this->custom_box['fields'] as $k => $v){
			$this->custom_box['fields'][$k]['id'] = $k;
		}
		return $this;
	}
	
	/**
	 * Sets the thumbnail metas names
	 * @param string $anchor the meta name for the anchor href
	 * @param string $overlay the meta name for the overlay text
	 * @return Custom_Feature_With_Metaboxes $this for chainability
	 */
	public function set_thumbnail_metas($anchor, $overlay){
		$this->thumbnail_metas = array(
			'anchor'	=>	$anchor,
			'overlay'	=>	$overlay
		);
		return $this;
	}
	
	/**
	 * (non-PHPdoc) Register hooks
	 * @see Custom_Feature::hook()
	 */
	public function hook(){
		parent::hook();
		add_action('admin_init', array(&$this, 'add_metaboxes'));
		add_action('save_post', array(&$this, 'save_metaboxes'), 10, 2);
	}
	
	/**
	 * Adds the needed metaboxed for the porfolio posts
	 */
	public function add_metaboxes(){
		add_meta_box(
			$this->custom_box['id'],
			$this->custom_box['title'],
			array(&$this, 'metabox'),
			$this->custom_post_labels['slug'],
			'normal',
			'high'
		);
	}
	
	/**
	 * Url metabox manager: prints the fields and checks them
	 * By default this prints a list of text input with labels.
	 * Overload it to have more customization power 
	 */
	public function metabox(){
		global $post;
		$toret = '';
		if(count($this->custom_box['fields'])){
			foreach($this->custom_box['fields'] as $k => $field){
				$field['id'] = $k;
				$value = get_post_meta( $post->ID, $field['id'], true );
				if(!empty($value)){ $field['value'] = $value; }
				$toret .= '<p>';
				$toret .= '<label for="'.$field['id'].'">'.$field['label'].'</label><br>';
				if(is_array($field['values'])){
					$toret .= $field['value'];
					$toret .= '<select id="'.$field['id'].'" name="'.$field['id'].'">';
					foreach($field['values'] as $option){
						$selected = '';
						if($option == $value){
							$selected = ' selected="selected"';
						}
						$toret .= '<option value="'.$option.'"'.$selected.'>'.$option.'</option>';
					}
					$toret .= '</select>';
				} else {
					$toret .= '<input class="large-text" id="'.$field['id'].
					'" name="'.$field['id'].'" type="text" value="'.$field['value'].'" />';
				}
				$toret .= '	</p>';
			}
		}
		echo $toret;		
	}
	
	/**
	 * Process the custom metabox fields
	 */
	public function save_metaboxes( $post_id ) {
		global $post;
		if(count($this->custom_box['fields'])){
			foreach($this->custom_box['fields'] as $field){
				if(!empty($_POST[$field['id']])){
					if(
						is_array($this->custom_box['fields'][$field['id']]['values']) 
						&& !in_array($_POST[$field['id']], $this->custom_box['fields'][$field['id']]['values'])
					) {
						wp_die(__('The Zone you\'ve entered is not allowed!', self::textdomain));
					} else {
						update_post_meta($post->ID, $field['id'], $_POST[$field['id']]);
					}
				}
			}
		}
	}
}

/**
 * Manage the portfolio feature 
 * @author etessore
 */
class Portfolio extends Custom_Feature_With_Metaboxes{
	
	public $custom_taxonomy_labels = array();
	
	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'clients',
			'label'			=>	'Clients',
			'sing-label'	=>	'Client',
			'description'	=>	'Our Clients'
		);
		
		$hooks = array(
			'shortcodes'					=>	array(
				'portfolio-random-sized-thumbs'	=>	array(&$this, 'random_sized_thumbs'),
				'portfolio-list-thumbs'			=>	array(&$this, 'list_thumbs'),
				'portfolio-fixed-sized-thumbs'	=>	array(&$this, 'fixed_sized_thumbs'),
				'portfolio-highlighted-client'	=>	array(&$this, 'highlighted_client'),
				'portfolio-clients'				=>	array(&$this, 'clients_list'),
				'portfolio-zones-list'			=>	array(&$this, 'subtitle_manager')
			),
			'subtitle_shortcode'			=>	'portfolio-zones-list'
		);
		
		$custom_box = array(
			'id'		=>	'custom-box-clients',
			'title'		=>	__('Customer Details', self::textdomain),
			'fields'	=>	array(
				'siteurl'	=>	array(
					'label'		=>	__('Url:', self::textdomain),
					'value'		=>	'http://'
				),
				'urldesc'	=>	array(
					'label'		=>	__('Description:', self::textdomain),
					'value'		=>	''
				)
			)
		);
		
		$custom_taxonomy_labels = array(
			'slug'			=>	'zone',	
			'label'			=>	'Zones',
			'sing-label'	=>	'Zone'
		);
		
		$this
			->set_custom_post($custom_post_labels)
			->set_custom_taxonomy($custom_taxonomy_labels)
			->set_custom_box($custom_box)
			->set_hooks($hooks)
			->set_subtitle_shortcut($this->hooks['subtitle_shortcode'])
			->set_thumbnail_metas('siteurl', 'urldesc')
			->setup();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::register_custom_taxonomy()
	 */
	public function register_custom_taxonomy(){
		parent::register_custom_taxonomy();
		register_taxonomy(
				'client-type',
				array(0 => $this->custom_post_labels['slug']),
				array(
					'hierarchical' => false,
					'label' => __('Types', self::textdomain),
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => true,
					'singular_label' => __('Type', self::textdomain)
				)
		);
		return $this;
	}
	
	/**
	 * Set the portfolio up.
	 * If this was a plugin this method should be called on enable
	 */
	public function setup(){
		parent::setup();
		return $this
			->thumbnail_sizes()
			->register_custom_taxonomy()
			->set_post_permalink_with_hash()
			->set_taxonomy_permalink_with_hash();
	}
	
	/**
	 * Retrieve a dictionary of zones with posts
	 */
	public function get_ordered_by_zones(){
		$zones = $this->get_taxonomy_entries();
		$ordered_post = array();
		
		if(count($zones)){
			foreach($zones as $zone){
				$ordered_post[$zone->name] = get_posts(array(
					'post_type' 	=>	$this->custom_post_labels['slug'],
					'taxonomy' 		=>	$zone->taxonomy,
					'term' 			=>	$zone->slug,
					'nopaging' 		=>	true, // to show all posts in this category, could also use 'numberposts' => -1 instead
			     ));
			}
		}
		
		return $ordered_post;
	}
	
	/**
	 * Manages images sizes for the portfolio
	 * @return Portfolio $this for chainability
	 */
	public function thumbnail_sizes(){
		add_theme_support('post-thumbnails');
		//add_image_size('portfolio-thumb-2', 140, 120, true);
		add_image_size('portfolio-thumb-3', 220, 120, false);
		//add_image_size('portfolio-thumb-4', 300, 120, true);
		return $this;
	}
	
	/**
	 * Get the markup for the list of the web package clients thumbs
	 * @param unknown_type $atts
	 */
	public function list_thumbs($atts){
		$posts = get_posts(array(
			'post_type'		=>	$this->custom_post_labels['slug'],
			'numberposts'	=>	(is_numeric($atts['number'])) ? $atts['number'] : 10,
			'post_status'	=>	'publish',
			'tax_query'		=>	array(
				array(
					'taxonomy'	=>	'client-type',
					'field'		=>	'slug',
					'terms'		=>	'webpack'
				)
			)
		));
		$toret = '';
		foreach($posts as $post){
			$toret .= $this->thumbnail_with_overlay($post);
		}
		return $toret;		
	}
	
	/**
	 * Get the markup for the list of portfolio thumbs with random sizes
	 * @param array $atts attributes passed by shortcode
	 * @return string html markup
	 */
	public function random_sized_thumbs($atts){
		$posts = get_posts(array(
			'post_type'		=>	$this->custom_post_labels['slug'],
			'numberposts'	=>	(is_numeric($atts['number'])) ? $atts['number'] : 10,
			'post_status'	=>	'publish'
		));
		
		$toret = '';
		$count = count($posts);
		$permutations = array(
			'3' => array(
				array(4,4,4)	
			),
			'4' => array(
				array(4,4,2,2),
				array(4,3,3,2),
				array(3,3,3,3)	
			),
			'5'	=> array(
				array(2,2,2,2,4)
			),
			/*'6' => array(
				array(2,2,2,2,2,2)
			)*/
		);
		
		$module = $count % 4;
		$n5 = $n3 = 0;
		switch($module){
			case 1:
			case 2:
				$n5 = $module;
				break;
			case 3:
				$n3 = 1;
				break;
		}
		
		$n4 = intval($count / 4) - $n5;
		
		$rows = array();
		for($i=0; $i<$n3; $i++){
			array_push($rows, '3');
		}
		for($i=0; $i<$n4; $i++){
			array_push($rows, '4');
		}
		for($i=0; $i<$n5; $i++){
			array_push($rows, '5');
		}
		
		shuffle($rows);
		
		if($count){
			global $post;
			$n_post = 0;
			foreach($rows as $k => $n_images){
				shuffle($permutations[$n_images]);
				$choosed_permutation = $permutations[$n_images][0];
				shuffle($choosed_permutation);
				foreach($choosed_permutation as $size){
					$post = $posts[$n_post++];		
					setup_postdata($post);
					$url = theme_helpers::anchor(
						get_post_meta( $post->ID, 'urllink', true ), 
						get_post_meta( $post->ID, 'urldesc', true )
					);
					$img_big = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
					$toret .= '<div class="portfolio-element random-size">';
					$toret .= $this->thumbnail_with_overlay(null, 'portfolio-thumb-'.$size);
					$toret .= '</div>';
					wp_reset_postdata();
				}
			}
		}
		
		return $toret;
	}
	
	/**
	 * Get the markup for the fixed size thumbnails.
	 * @param array $atts attributes passed by shortcode
	 * @return string html markup
	 */
	public function fixed_sized_thumbs($atts){
		$posts = get_posts(
			array(
				'post_type'		=>	$this->custom_post_labels['slug'],
				'post_status'	=>	'publish',
				'numberposts'	=>	(is_numeric($atts['number'])) ? $atts['number'] : 9
			)
		);
		return $this->horizontal_scroll($posts, 3, 'clients', false).
			$this->full_list_cta();
		/*$toret = '';
		$count = count($posts);
		if($count){
			wp_enqueue_script('cycle-init');
			global $post;
			wp_enqueue_style('portfolio');
			$toret .= '<div class="cycle portfolio" data-fx="scrollHorz"><div class="cycle-element">';
			foreach($posts as $k => $post){
				setup_postdata($post);
				
				if($k && $k%3==0){
					$toret .=  '</div><div class="cycle-element">';
				}
				
				$img = $this->thumbnail_with_overlay();
				
				$tpl = '<div class="portfolio-element fixed-size">%s</div>';
				
				$toret .= sprintf($tpl,$img);
				wp_reset_postdata();
			}
			$toret .= '</div></div>';
			if($count > 3){
				wp_enqueue_script('cycle');
				$toret .= self::get_prev_next();
			}
		}
		return '<div class="portfolio-fixed-size-thumbs relative">'.
			$toret.'<div class="slide-margin-fixer">'.
			$this->full_list_cta().'</div>'.'</div>';*/
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::horizontal_scroll_element()
	 */
	public function horizontal_scroll_element(){
		return $this->thumbnail_with_overlay();
	}
	
	public function get_bottom_of_the_slide(){
		return '';
	}

	
	/**
	 * Get the markup for the highlighted client
	 * @param array $atts attributes passed by shortcode
	 */
	public function highlighted_client($atts){
		if(is_numeric($atts['id'])) {
			$post = get_post($atts['id']);
		} else {
			$post = $this->get_posts(
				array(
					'numberposts'     => 1,
				    'orderby'         => 'rand'
				)	
			);
			$post = get_post($post[0]->ID);
		}
		
		$img = $this->thumbnail_with_overlay($post);
		$title = get_the_title($post->ID);
		$body = apply_filters('get_the_excerpt', $post->post_content);
		
		return <<< EOF
			<div class="highlighted-client clearfix">
				<div class="image">$img</div>
				<div class="title second-level">$title</div>
				<div class="body">$body</div>
			</div>
EOF;
	}
	
	/**
	 * Retrieves the markup for the full client list
	 * @param array $atts
	 */
	public function clients_list($atts){
		$posts = $this->get_posts(array('numberposts'=>-1));
		$ordered_post = array();
		if(count($posts)){
			echo '<div class="clients-full-list">';
			global $post;
			foreach($posts as $post){
				$ordered_post[get_post_meta($post->ID, $this->custom_box['fields']['zone']['id'], true)][] = $post;
			}
			foreach($ordered_post as $zone => $posts){
				echo '<div class="title">'.$zone.'</div>';
				foreach($posts as $post){
					setup_postdata($post);
					echo '<div class="client">'.self::thumbnail_with_overlay().'</div>';
				}
			}
			wp_reset_postdata();
			echo '</div>';
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::thumbnail_with_overlay()
	 */
	/*public function thumbnail_with_overlay($post=null, $size='portfolio-thumb-3'){
		if(is_null($post)){
			global $post;
		}
		if(is_numeric($post)){
			$post = get_post($post);
		}
		$post_meta = trim(get_post_meta( $post->ID, 'urldesc', true ));
		$desc = (empty($post_meta))
		? get_the_title($post->ID)
		: $post_meta;
		$big_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
		wp_enqueue_script('fancybox-init');
		wp_enqueue_style('fancybox');
		return theme_helpers::hash_placeholder(sanitize_title($post->post_title)).theme_helpers::anchor(
				$big_img[0],
				get_the_post_thumbnail(
						$post->ID,
						$size
				).'<span class="overlay">'.$desc.'</span>',
				array(
						'id'			=>	sanitize_title($post->post_title),
						'class'			=>	$this->custom_post_labels['slug'].'-thumb thumb-with-overlay fancybox relative',
						'rel'			=>	$this->custom_post_labels['slug'].'-group',
						'data-url'		=>	esc_attr(get_post_meta( $post->ID, 'urllink', true )),
						'data-label'	=>	esc_attr(get_post_meta( $post->ID, 'urldesc', true ))
				)
		);
	}*/
	
	
}

/**
 * Portfolio initialization
 */
global $portfolio;
$portfolio = new Portfolio();



/**
 * News feture
 * @author etessore
 */
class News extends Custom_Feature{
	const custom_post_name = 'news';
	
	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'news',
			'label'			=>	'News',
			'sing-label'	=>	'News',
			'description'	=>	'Some breaking news from the company'
		);
		
		$hooks = array(
			'shortcodes'	=>	array(
				'events'		=>	array(&$this, 'events_list'),
			)
		);
		$this
			->set_custom_post($custom_post_labels)
			->set_hooks($hooks)
			->setup();
	}
	
	/**
	 * Callback for the shortcode
	 * @param array $atts
	 */
	public function events_list($atts){
		wp_enqueue_script('management-init');
		return $this->horizontal_scroll(
			$this->get_posts(array('numberposts'=>9)),
			3,
			'expandable-details textonly'
		);
	}
	
	
}

global $news;
$news = new News();

class Press extends Custom_Feature{
	const custom_post_name = 'news';

	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'press',
			'label'			=>	'Press',
			'sing-label'	=>	'Press',
			'description'	=>	'Some press stuff from the company'
		);

		$hooks = array(
			'shortcodes'	=>	array(
				'press'		=>	array(&$this, 'press_list'),
			)
		);
		$this
		->set_custom_post($custom_post_labels)
		->set_hooks($hooks)
		->setup();
	}

	/**
	 * Get the markup for the list of press entries
	 * @param array $atts attributes passed by shortcode
	 */
	public function press_list($atts){
		return $this->horizontal_scroll(
			$this->get_posts(array('numberposts'=>9)),
			3,
			'expandable-details textonly'
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::horizontal_scroll_element()
	 */
	public function horizontal_scroll_element(){
		$title		=	get_the_title();
		$excerpt	=	get_the_excerpt();
		$content	=	apply_filters('the_content', get_the_content());
		$read_more	=	__('Read More', self::textdomain);
		$pdf		=	theme_helpers::pdf_or_contact_cta(null, __('PDF Version', self::textdomain));
		
		return <<< EOF
			<div class="single-element relative">
				<h3 class="title second-level">
					$title
				</h3>
				<div class="body">
					$excerpt
				</div>
				<div class="more">
					<a class="sprite cta-2" href="javascript:;">$read_more</a>
				</div>
				<div class="description">
					<div class="title">$title</div>
					$content
					<div class="pdf">$pdf</div>
				</div>
			</div>
EOF;
	}


}

global $press;
$press = new Press();

class Newsletters extends Custom_Feature{

	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'newsletter',
			'label'			=>	'Newsletters',
			'sing-label'	=>	'Newsletter',
			'description'	=>	'Some company newsletter'
		);

		$hooks = array(
			'shortcodes'	=>	array(
				'newsletter'		=>	array(&$this, 'newsletter_list'),
			)
		);
		$this
		->set_custom_post($custom_post_labels)
		->set_hooks($hooks)
		->setup();
	}

	/**
	 * Get the markup for the newsletters list
	 * @param array $atts attributes passed by the shordcode
	 */
	public function newsletter_list($atts){
		return $this->horizontal_scroll(
			$this->get_posts(array('numberposts'=>9)),
			3,
			'expandable-details textonly'
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::horizontal_scroll_element()
	 */
	public function horizontal_scroll_element(){
		$title		=	get_the_title();
		$excerpt	=	get_the_excerpt();
		$content	=	apply_filters('the_content', get_the_content());
		$read_more	=	__('Read More', self::textdomain);
		$pdf		=	theme_helpers::pdf_or_contact_cta(null, __('PDF Version', self::textdomain));
		
		return <<< EOF
			<div class="single-element relative">
				<h3 class="title second-level">
					$title
				</h3>
				<div class="body">
					$excerpt
				</div>
				<div class="more">
					<a class="sprite cta-2" href="javascript:;">$read_more</a>
				</div>
				<div class="description">
					<div class="title">$title</div>
					$content
					<div class="pdf">
						$pdf
					</div>
				</div>
			</div>
EOF;
	}


}

global $newsletter;
$newsletter = new Newsletters();


/**
 * Manages the Tesimoniy feature
 * @author etessore
 */
class Testimonies extends Custom_Feature_with_metaboxes{
	const custom_post_name = 'testimonies';
	const customer_box_id = 'testimony-box';
	
	
	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'testimonies',
			'label'			=>	'Testimonies',
			'sing-label'	=>	'Testimony',
			'description'	=>	'Some testimonies about us'
		);
		$hooks = array(
			'shortcodes'		=>	array(
				'testimony'		=>	array(&$this, 'testimonies_list')
			)
		);
		
		$custom_box = array(
				'id'		=>	'custom-box-clients',
				'title'		=>	__('Testimonial Details', self::textdomain),
				'fields'	=>	array(
						'siteurl'	=>	array(
								'label'		=>	__('Url:', self::textdomain),
								'value'		=>	'http://'
						),
						'urldesc'	=>	array(
								'label'		=>	__('Description:', self::textdomain),
								'value'		=>	''
						)
				)
		);
		
		$this
			->set_custom_post($custom_post_labels)
			->set_hooks($hooks)
			->set_custom_box($custom_box)
			->set_thumbnail_metas('siteurl', 'urldesc')
			->setup();
	}
	
	/**
	 * Get the markup for the testimonies list.
	 * @param array $atts attributes passed by shortcode
	 * @return string html markup
	 */
	public function testimonies_list($atts){
		$posts = $this->get_posts(
			array(
				'numberposts'	=>	(is_numeric($atts['number'])) ? $atts['number'] : 9
			)
		);
		return $this->horizontal_scroll($posts, 1, 'testimonies').$this->full_list_cta();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::horizontal_scroll_element()
	 */
	public function horizontal_scroll_element(){
		$title 	=	get_the_title();
		$image	=	$this->thumbnail_with_overlay();
		$body	=	apply_filters('the_content', get_the_content());
		return <<< EOF
			<div class="cycle-element">
				<div class="single-element">
					<h3 class="title second-level">$title</h3>
					<div class="image">$image</div>
					<div class="body">$body</div>
				</div>
			</div>	
EOF;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::get_bottom_of_the_slide()
	 */
	public function get_bottom_of_the_slide(){
		return;
	}
	
}


global $testimonies;
$testimonies = new Testimonies();


//TODO: extends Custom_Feature_With_Metaboxes
class Slideshow extends Custom_Feature{
	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'slideshows',
			'label'			=>	'Slideshows',
			'sing-label'	=>	'Slideshow',
			'description'	=>	'The content of the homepage slideshow'
		);
		$custom_box = array(
			'id'			=>	'slideshow-metabox',
			'title'			=>	'Slideshow',
			'fields' 		=>	array(
				array(
					'id'		=>	'siteurl',
					'label'		=>	__('Url:', self::textdomain),
					'value'		=>	'http://'
				),
				array(
					'id'		=>	'urldesc',
					'label'		=>	__('Description:', self::textdomain),
					'value'		=>	''
				)
			)
		);
		
		$this
			->disable_archives()
			->set_custom_post($custom_post_labels)
			->setup();
	}
	
	//TODO: port the simple fields features here
	public function metabox(){
		global $post;
		if(is_admin() && $post->post_type == $this->custom_post_labels['slug']){
			wp_register_script(
				'custom-media-uploader', 
				get_template_directory_uri().'/js/media.js', 
				array('jquery'), 
				'1.0', 
				true
			);
			wp_enqueue_script('custom-media-uploader');
		}
		?>
		<input 
			type="text" 
			class="text" 
			name="test" 
			id="test" 
			value="0"
		>
		<a 
			class="thickbox" 
			href="media-upload.php?type=image&amp;TB_iframe=true"
		>Select file</a>
		<?php 
	}
}

global $slideshows;
$slideshows = new Slideshow();


/**
 * Video feature
 * @author etessore
 */
class Videos extends Custom_Feature_With_Metaboxes{
	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'videos',
			'label'			=>	'Videos',
			'sing-label'	=>	'Video',
			'description'	=>	'Some interesting videos about us'
		);
		
		$hooks = array(
			'shortcodes'		=>	array(
				'videos'			=>	array(&$this, 'videos_list')
			)
		);
		
		$custom_box = array(
			'id'			=>	'video-metabox',
			'title'			=>	'Video',
			'fields' 		=>	array(
				array(
					'id'		=>	'youtubeid',
					'label'		=>	__('Youtube ID:', self::textdomain),
					'value'		=>	''
				)
			)
		);
		
		$this
			->set_custom_post($custom_post_labels)
			->set_custom_box($custom_box)
			->set_hooks($hooks)
			->setup();
	}
	
	/**
	 * Retrives the markup for the videos list
	 */
	public function videos_list(){
		$posts = $this->get_posts(
			array(
				'numberposts'	=>	(is_numeric($atts['number'])) ? $atts['number'] : 9
			)
		);
		$toret = '';
		$count = count($posts);
		if($count){
			global $post;
			wp_enqueue_style('videos');
			$toret .= '<div class="videos relative">';
			foreach($posts as $k => $post){
				setup_postdata($post);
				$yt_video_id = get_post_meta( $post->ID, 'youtubeid', true );
				$toret .= <<< EOF
					<span class="yt-thumb" data-video="$yt_video_id"></span>
EOF;
				wp_reset_postdata();
			}
			$toret .= '</div>';
			$toret .= 
				'<div class="video-player">'
				.theme_helpers::youtube_video(get_post_meta($posts[0]->ID, 'youtubeid', true))
				.'</div>';
			wp_enqueue_script('jquery-flash-init');
		}
		return $toret;
	}
}



global $videos;
$videos = new Videos();


class Partners extends Custom_Feature_With_Metaboxes{
	function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'partners',
			'label'			=>	'Partners',
			'sing-label'	=>	'Partner',
			'description'	=>	'Some of our partners'
		);
		$hooks = array(
			'shortcodes'		=>	array(
				'partners-type-list'=>	array(&$this, 'subtitle_manager'),
				'partners'			=>	array(&$this, 'partners_list'),
			),
			'subtitle_shortcode'			=>	'partners-type-list'
		);
		$custom_box = array(
			'id'		=>	'custom-box-clients',
			'title'		=>	__('Partner Details', self::textdomain),
			'fields'	=>	array(
				'siteurl'	=>	array(
					'label'		=>	__('Url:', self::textdomain),
					'value'		=>	'http://'
				),
				'urldesc'	=>	array(
					'label'		=>	__('Description:', self::textdomain),
					'value'		=>	''
				)
			)
		);
		$custom_taxonomy_labels = array(
			'slug'			=>	'type',	
			'label'			=>	'Types',
			'sing-label'	=>	'Type'
		);
		$this
			->set_custom_post($custom_post_labels)
			->set_custom_taxonomy($custom_taxonomy_labels)
			->set_hooks($hooks)
			->set_custom_box($custom_box)
			->set_subtitle_shortcut($this->hooks['subtitle_shortcode'])
			->set_thumbnail_metas('siteurl', 'urldesc')
			->setup();
	}
	
	/**
	 * Get the markup for the list of partners
	 * @param array $atts shortcode additional parameters
	 */
	public function partners_list($atts){
		$posts = $this->get_posts(array(
			'numberposts'	=>	(is_numeric($atts['number'])) ? $atts['number'] : 8
		));
		//return $this->horizontal_scroll($posts, 3, 'partners');
		$count = count($posts);
		$toret = '';
		if($count){
			global $post;
			wp_enqueue_script('cycle-init');
			$toret .= '<div class="cycle partners" data-fx="scrollHorz"><div class="cycle-element">';
			foreach($posts as $k => $post){
				setup_postdata($post);
				
				if($k && $k%3==0){
					$toret .=  '</div><div class="cycle-element">';
				}
				
				$toret .= $this->thumbnail_with_overlay();
				wp_reset_postdata();
			}
			$toret .= '</div></div>';
			if($count > 4){
				wp_enqueue_script('cycle');
				$toret .= self::get_prev_next();
			}
		}
		return '<div class="relative">'.$toret.'</div>';
	}
	
	/**
	 * Retrieve a dictionary of zones with posts
	 */
	public function get_ordered_by_zones(){
		$zones = $this->get_taxonomy_entries();
		$ordered_post = array();
		
		if(count($zones)){
			foreach($zones as $zone){
				$ordered_post[$zone->name] = get_posts(array(
					'post_type' 	=>	$this->custom_post_labels['slug'],
					'taxonomy' 		=>	$zone->taxonomy,
					'term' 			=>	$zone->slug,
					'nopaging' 		=>	true, // to show all posts in this category, could also use 'numberposts' => -1 instead
			     ));
			}
		}
		
		return $ordered_post;
	}
}

global $partners;
$partners = new Partners();


/**
 * Manages the management team entries
 * @author etessore
 */
class Managers extends Custom_Feature_With_Metaboxes{
	function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'managers',
			'label'			=>	'Managers',
			'sing-label'	=>	'Manager',
			'description'	=>	'Our Management Team'
		);
		$hooks = array(
			'shortcodes'	=>	array(
				'management-team'=>	array(&$this, 'management_team')
			)
		);
		$custom_box = array(
			'id'			=>	'custom-box-managers',
			'title'			=>	__('Manager Details', self::textdomain),
			'fields'		=>	array(
				'job-name'		=>	array(
					'label'			=>	__('Job Name:', self::textdomain),
					'value'			=>	''
				),
				'mail'			=>	array(
					'label'			=>	__('Mail:', self::textdomain),
					'value'			=>	'@fastbooking.net'
				)
			)
		);
		$this
			->set_custom_post($custom_post_labels)
			->set_hooks($hooks)
			->set_custom_box($custom_box)
			->setup();
	}
	
	public function get_manager_title($post=null){
		if(is_null($post)){ global $post; }
		return get_post_meta( $post->ID, $this->custom_box['fields']['job-name']['id'], true );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::horizontal_scroll_element()
	 */
	public function horizontal_scroll_element(){
		global $post;
		$title		=	get_the_title();
		$subtitle	=	$this->get_manager_title();
		$excerpt	=	$this->get_excerpt();
		$content	=	apply_filters('the_content', get_the_content());
		$read_more	=	__('Read More', self::textdomain);
		
		return <<< EOF
			<div class="single-element relative">
				<h2 class="title second-level">
					$title
				</h2>
				<h3 class="title trd-level">
					$subtitle
				</h3>
				<div class="body">
					$excerpt
				</div>
				<div class="more">
					<a class="sprite cta-2" href="javascript:;">$read_more</a>
				</div>
				<div class="description">
					<div class="title">
						$title
					</div>
					<div class="title trd-level">
						$subtitle
					</div>
					$content
				</div>
			</div>
EOF;
	}
	
	/**
	 * Retrieves the markup for the management team list
	 */
	public function management_team(){
		wp_enqueue_script('management-init');
		return $this->horizontal_scroll(
			$this->get_posts(
				array(
					'numberposts'=>-1,
					'orderby'         => 'post_date',
					'order'           => 'ASC',
				)
			),
			3,
			'expandable-details textonly'
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature_With_Metaboxes::save_metaboxes()
	 */
	public function save_metaboxes( $post_id ) {
		global $post;
		parent::save_metaboxes($post_id);
		
		if(!empty($_POST['manager-excerpt'])){
			update_post_meta($post->ID, 'manager-excerpt', $_POST['manager-excerpt']);
		}
	}
	
	/**
	 * Retrives the job name
	 * @param int|object $post the post, if null get the current
	 * @return string the job name
	 */
	function get_job_name($post=null){
		if(is_null($post)){
			global $post;
			$post_id = $post->ID;
		}
		if(is_numeric($post)){
			$post_id = $post;
		}
		return get_post_meta($post_id, $this->custom_box['fields']['job-name']['id'], true);
	}
	
	/**
	 * Retrieves the manager brief description
	 * @param object $post the post
	 */
	public function get_excerpt($post=null){
		if(is_null($post)){
			global $post;
			$post_id = $post->ID;
		}
		if(is_numeric($post)){
			$post_id = $post;
		}
		return apply_filters(
			'the_content',
			get_the_excerpt()
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::thumbnail_with_overlay()
	 */
	public function thumbnail_with_overlay($post=null, $size='portfolio-thumb-3'){
		if(is_null($post)){ global $post; }
		if(is_numeric($post)){ $post = get_post($post); }
		return theme_helpers::anchor(
			'javascript:;',
			$this->thumbnail(
				$post->ID, 
				$size
			).'<span class="overlay">'.get_the_title().'</span>',
			array(
				'id'	=>	sanitize_title($post->post_title),
				'class'	=>	$this->custom_post_labels['slug'].'-thumb thumb-with-overlay relative',
			)
		);
	}
	
	/**
	 * Retrieves the markup for the box with image and cta button
	 */
	public function thumbnail_with_cta($post=null, $size='portfolio-thumb-3'){
		wp_enqueue_script('management-init');
		if(is_null($post)) {
			global $post;
		}
		if(is_numeric($post)){
			$p = get_post($post);
			global $post;
			$post = $p;
		}
		setup_postdata($post);
		$toret =
		'<div class="manager single-element">'
		.$this->thumbnail_with_overlay($post, $size)
		//.'<div class="name">'.$desc.'</div>'
		.'<div class="job-name">'.$this->get_job_name($post->ID).'</div>'
		.'<div class="more"><a class="sprite cta" href="javascript:;">'.__('Read More', self::textdomain).'</a></div>'
		.'<div class="description">'.'<div class="title">'.get_the_title().'</div>'.apply_filters('the_content', get_the_content()).'</div>'
		.'</div>';
		wp_reset_postdata();
		return $toret;
	}
}

global $management_team;
$management_team = new Managers();

/**
 * Manages the Key Figures feature
 * @author etessore
 */
class Key_Figures extends Custom_Feature_With_Metaboxes{
	
	public function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'key-figures',
			'label'			=>	'Key Figures',
			'sing-label'	=>	'Figure',
			'description'	=>	'Our key figures'
		);
		$hooks = array(
			'shortcodes'	=>	array(
				'key-figures'	=>	array(&$this, 'key_figures_list')
			)
		);
		$this
			->set_custom_post($custom_post_labels)
			->set_hooks($hooks)
			->setup();
	}
	
	/**
	 * Retrieves the markup for the list of key figures
	 * @param array $atts attributes list
	 */
	public function key_figures_list($atts){
		return $this->horizontal_scroll(
			$this->get_posts(),
			1,
			'keyfigures-team'
		);
		$toret = '';
		$posts = $this->get_posts();
		if(count($posts)){
			$toret .= '<div class="keyfigures-team relative"><div class="cycle" data-fx="scrollHorz">';
			foreach($posts as $k => $post){
				$toret .= '<div class="cycle-element">'.$this->thumbnail_with_overlay($post, 'key-figure').'</div>';
			}
			$toret .= '</div>'.self::get_prev_next().'</div>';
		}
		return $toret;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::horizontal_scroll_element()
	 */
	public function horizontal_scroll_element(){
		global $post;
		$image		=	$this->thumbnail_with_overlay($post, 'key-figure');
		
		return <<< EOF
			<div class="single-element relative">$image</div>
EOF;
	}

}

global $key_figures;
$key_figures = new Key_Figures();

/**
 * Manages the recruitments feature
 * @author etessore
 */
class Recruitments extends Custom_Feature_With_Metaboxes{
	function __construct(){
		$custom_post_labels = array(
			'slug'			=>	'recruitments',
			'label'			=>	'Recruitments',
			'sing-label'	=>	'Job Name',
			'description'	=>	'Our open positions'
		);
		$hooks = array(
			'shortcodes'		=>	array(
				'recruitments'		=>	array(&$this, 'recruitments_list'),
				'rec-zones'			=>	array(&$this, 'taxonomy_entries_list')
			),
			//'subtitle_shortcode'	=>	'rec-zones'
		);
		$custom_taxonomy_labels = array(
			'slug'			=>	'recruitment-zone',
			'label'			=>	'Recruitment Zones',
			'sing-label'	=>	'Zone'
		);
		$this
			->set_custom_post($custom_post_labels)
			->set_custom_taxonomy($custom_taxonomy_labels)
			->set_hooks($hooks)
			->set_subtitle_shortcut($this->hooks['subtitle_shortcode'])
			->setup();
	}
	
	/**
	 * Retrives the markup for the recruitments list
	 */
	public function recruitments_list(){
		wp_enqueue_script('management-init');
		return $this->horizontal_scroll(
			$this->get_posts(array('numberposts'=>-1)),
			3,
			'expandable-details textonly'
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::navmenu()
	 */
	public function navmenu($items, $menu, $args){
		$menu_order = 0;
		$toret = array();
		foreach($items as $k => $item){
			if(strpos($item->url, '%last-'.$this->custom_post_labels['slug'].'%')!==false){
				$posts = $this->get_posts(array('numberposts'=>3));
				foreach($posts as $post){
					$post->menu_item_parent = $item->menu_item_parent;
					$post->post_type = 'nav_menu_item';
					$post->object = 'custom';
					$post->type = 'custom';
					$post->menu_order = ++$menu_order;
					$post->title = $post->post_title;
					$post->url = get_permalink( $post->ID );
					/* add as a child */
					$toret[] = $post;
						
				}
			} elseif(strpos($item->url, '%list-'.$this->custom_taxonomy_labels['slug'].'%')!==false) {
				$posts = $this->get_taxonomy_entries();
				foreach($posts as $post){
					$post->menu_item_parent = $item->menu_item_parent;
					$post->post_type = 'nav_menu_item';
					$post->object = 'custom';
					$post->type = 'custom';
					$post->menu_order = ++$menu_order;
					$post->title = $post->name;
					$post->url = get_term_link( intval($post->term_id), $this->custom_taxonomy_labels['slug'] );
					/* add as a child */
					$toret[] = $post;
	
				}
			} else {
				$item->menu_order = ++$menu_order;
				$toret[] = $item;
	
			}
		}
		return $toret;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Custom_Feature::horizontal_scroll_element()
	 */
	public function horizontal_scroll_element(){
		$title		=	get_the_title();
		$excerpt	=	get_the_excerpt();
		$content	=	apply_filters('the_content', get_the_content());
		$read_more	=	__('Read More', self::textdomain);
		$contact	=	theme_helpers::contact_us(
			'class="cta sprite"', 
			__('Send your Resumee', self::textdomain)
		);
	
		return <<< EOF
			<div class="single-element relative">
				<h3 class="title second-level">
					$title
				</h3>
				<div class="body">
					$excerpt
				</div>
				<div class="more">
					<a class="sprite cta-2" href="javascript:;">$read_more</a>
				</div>
				<div class="description">
					<div class="title">$title</div>
					$content
					<div class="send-resumee-cta">$contact</div>
				</div>
			</div>
EOF;
	}
	
	
}

global $recruirments;
$recruitments = new Recruitments();



/**
 * This class removes the default excerpt metabox
 * and adds a new box with the wysiwyg editor capability
 * @author etessore
 */
class TinyMceExcerptCustomization{
	const textdomain = '';
	const custom_exceprt_slug = '_custom-excerpt';
	var $contexts;

	/**
	 * Set the feature up
	 * @param array $contexts a list of context where you want the wysiwyg editor in the excerpt field. Defatul array('post','page')
	 */
	function __construct($contexts=array('post', 'page')){
		
		$this->contexts = $contexts;
		
		add_action('admin_menu', array($this, 'remove_excerpt_metabox'));
		add_action('add_meta_boxes', array($this, 'add_tinymce_to_excerpt_metabox'));
		add_filter('wp_trim_excerpt',  array($this, 'custom_trim_excerpt'), 10, 2);
		add_action('save_post', array($this, 'save_box'));
	}
	
	/**
	 * Removes the default editor for the excerpt
	 */
	function remove_excerpt_metabox(){
		foreach($this->contexts as $context)
			remove_meta_box('postexcerpt', $context, 'normal');
	}
	
	/**
	 * Adds a new excerpt editor with the wysiwyg editor
	 */
	function add_tinymce_to_excerpt_metabox(){
		foreach($this->contexts as $context)
		add_meta_box(
			'tinymce-excerpt', 
			__('Excerpt', self::textdomain), 
			array($this, 'tinymce_excerpt_box'), 
			$context, 
			'normal', 
			'high'
		);
	}
	
	/**
	 * Manages the excerpt escaping process
	 * @param string $text the default filtered version
	 * @param string $raw_excerpt the raw version
	 */
	function custom_trim_excerpt($text, $raw_excerpt) {
		global $post;
		$custom_excerpt = get_post_meta($post->ID, self::custom_exceprt_slug, true);
		if(empty($custom_excerpt)) return $text;
		return $custom_excerpt;
	}

	/**
	 * Prints the markup for the tinymce excerpt box
	 * @param object $post the post object
	 */
	function tinymce_excerpt_box($post){
		$content = get_post_meta($post->ID, self::custom_exceprt_slug, true);
		if(empty($content)) $content = '';
		wp_editor(
			$content,
			self::custom_exceprt_slug,
			array(
				'wpautop'		=>	true,
				'media_buttons'	=>	false,
				'textarea_rows'	=>	10,
				'textarea_name'	=>	self::custom_exceprt_slug
			)
		);
	}
	
	/**
	 * Called when the post is beeing saved
	 * @param int $post_id the post id
	 */
	function save_box($post_id){
		update_post_meta($post_id, self::custom_exceprt_slug, $_POST[self::custom_exceprt_slug]);
	}
}

global $tinymce_excerpt;
$tinymce_excerpt = new TinyMceExcerptCustomization(array('post','page','managers', 'recruitments'));




/**
 * News feture
 * @author etessore
 */
class Contacts extends Custom_Feature{

	public function __construct(){
		$custom_post_labels = array(
				'slug'			=>	'contacts',
				'label'			=>	'Contacts',
				'sing-label'	=>	'Contact',
				'description'	=>	'The company contacts'
		);

		$hooks = array(
				'shortcodes'	=>	array(
						'contacts'		=>	array(&$this, 'contacts_list'),
				)
		);
		$this
		->set_custom_post($custom_post_labels)
		->set_hooks($hooks)
		->setup();
	}

	/**
	 * Callback for the shortcode
	 * @param array $atts
	 */
	public function contacts_list($atts){
		wp_enqueue_script('management-init');
		return $this->horizontal_scroll(
				$this->get_posts(array('numberposts'=>9)),
				3,
				'expandable-details textonly'
		);
	}


}

global $contacts;
$contacts = new Contacts();