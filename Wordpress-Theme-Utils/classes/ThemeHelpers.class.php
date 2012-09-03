<?php 
class ThemeHelpers{
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
	//static $use_hash_permalink = true;
	
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
	public static function head(){
		$tempate_directory_uri = get_template_directory_uri();
		$charset = get_bloginfo('charset');
		$author = self::author;
		$title = fbseo_get_title();
		$description = fbseo_get_metadescription();
		
		echo <<<EOF
	<title>$title</title>
	<meta name="description" value="$description" />
    <meta charset="$charset">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="$author">
    <link rel="shortcut icon" href="$tempate_directory_uri/images/favicon.png">
EOF;
		if(self::debug){
			echo <<<EOF

	<style>
		.debug{margin:10px;border:1px solid red;font-size:150%;}
	</style>
EOF;
		}
	}
	
	/**
	 * @return the markup for the mini gallery
	 * @param object $post the post object of wich get the gallery
	 */
	public static function get_gallery($post=null){
		if(!isset($post)) global $post;
		$images = fb_get_all_images($post->ID, array(), array('slideshow', 'nogallery', 'highlight'), false);
		
		$gallery = new MinigalleryBigImageWithThumbs();
		$gallery
			->set_markup('next', '<div class="next small sprite"></div>')
			->set_markup('prev', '<div class="prev small sprite"></div>')
			->add_images($images);
		return $gallery->get_markup();

	}
	
	public static function get_photogallery($post=null){
		if(!isset($post)) global $post;
		$images = fb_get_all_images($post->ID, array(), array('slideshow', 'nogallery', 'highlight'), false);
		
		$gallery = new PhotogalleryThumbWithFancybox();
		$gallery
			->set_markup('next', '<div class="next small sprite"></div>')
			->set_markup('prev', '<div class="prev small sprite"></div>')
			->add_images($images);
		return $gallery->get_markup();
		
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
		<div class="debug">
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
	 * Get the markup for an <a> tag
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
	 * Get the markup for a <img> tag
	 * @param string $src the image source
	 * @param array|string $parms additional parameters
	 */
	public static function image($src, $parms=''){
		$src = esc_attr($src);
		$parms 	= trim(self::array_to_html_attributes('=', $parms));
		if(!empty($parms)) $parms = ' '.$parms;
		return <<< EOF
		<img src="$src"$parms >
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
	 * Retrieves the markup for printing offers
	 * @param array $attr attributes for the current offer set
	 * TODO: move the default hid to the cms
	 */
	public static function iframe_offers($attr=array()){ 
		$attr = array_merge(array(
			'hid'		=>	'itfin24605',
			'nb'		=>	1,
			'order'		=>	'random',
			'cta'		=>	__('Check Availability', self::textdomain),	
			'ctam'		=>	__('Info', self::textdomain),
			'displayPrice'	=>	'1',
			'apd'		=>	__('From', self::textdomain),
			'pn'		=>	__('Per Night', self::textdomain),
			'pb_flag'	=>	1
		), $attr);
		wp_enqueue_style(
			'snippet', 
			get_template_directory_uri().'/css/snippet.css', 
			null, 
			'0.1', 
			'screen'
		);
		$url_arr = parse_url('http://hotelsitecontents.fastbooking.com/promotions.php');
		$url_arr['query'] = http_build_query($attr);
		$url = self::build_url($url_arr);
		return <<< EOF
	<div id="FB_so" class="clearfix"></div>
	<iframe id="iframe_FB_so" src="$url" style="display:none"></iframe>
	<script type="text/javascript" src="http://hotelsitecontents.fastbooking.com/js/com.js"></script>
EOF;
	}
	
	public static function fbqs(){
		wp_enqueue_script('fbqs');
		wp_enqueue_style('datepicker');
		wp_enqueue_style('fbqs');
		return '<div id="fastbooking_qs" class="grid_4 alpha omega">'.self::loading().'</div>';
	}
	
	public static function loading(){
		return '<div class="loading">'.__('Loading...', ThemeHelpers::textdomain).'</div>';
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
		$h1		=	fbseo_get_h1();
		$extra	=	fbseo_get_h1_extra();
		return <<< EOF
	<h1>$h1</h1>
	<span>$extra</span>
		
EOF;
	}
	
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




/**
 * Quick and dirty way to know a variable value
 * vd stand for <strong>v</strong>ar_dump() and <strong>d</storng>ie()
 * @param mixed $var the variable to be dumped
 */
function vd($var){
	ThemeHelpers::debug($var, 'DEBUG', true, false, true);
}

/**
 * Quick and dirty way to know a variable value
 * Usefull in a loop cause it doesn't break the execution with die
 * @param mixed $var the variable to be dumped
 */
function v($var){
	ThemeHelpers::debug($var, 'DEBUG', true, false, false);
}
