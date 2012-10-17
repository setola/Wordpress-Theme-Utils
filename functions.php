<?php
include_once dirname(__FILE__) . '/assets.php';
include_once dirname(__FILE__) . '/classes/ClassAutoloader.class.php';

/**
 * Initialize the autoloader
 */
new ClassAutoloader();

/**
 * Initialize the debug utils vd() v() vc()
 */
new DebugUtils();

/**
 * Iframe system
 */
global $offers;
$offers = new SpecialOffersSnippet('itqua22319');
$offers
	->add_param('displayPrice', 1)
	->add_param('nb', 1)
	->add_param('order', 'random')
	->add_param('pb_flag', 1)
	->add_param('apd', __('Starting from ', 'theme'))
	->add_param('pn', __(' per night', 'theme'))
	->add_param('cta', __('Check Availability', 'theme'))
	->add_param('ctam', __('More Info', 'theme'));

global $runtime_infos;
$runtime_infos = new RuntimeInfos();
$runtime_infos->hook();

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

/**
 * Hook some functions to wp (After WP object is set up)
 */
//add_action('wp', array('ThemeHelpers', 'head'));


/**
 * Image sizes for this theme
 */
//add_theme_support('post-thumbnails');
//set_post_thumbnail_size(940, 220, true);
//add_image_size('rooms', 340, 220, true);

/**
 * Remove some useless css and js by wpml
 */
define(ICL_DONT_LOAD_LANGUAGES_JS, true);
define(ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS, true);
define(ICL_DONT_LOAD_NAVIGATION_CSS, true);

/**
 * Prints the language menu
 */
function the_language_menu(){
	do_action('icl_language_selector');
}

/**
 * Prints the slideshow
 */
function the_slideshow(){
	$preloader = new Slideshow();
	$preloader->the_markup();
}


/**
 * Manages the slideshow
 * @author etessore
 * @version 1.0.0
 */
class Slideshow extends ImagePreload{
	/**
	 * Initializes the parent object to the default values
	 */
	function __construct(){
		$tpl = <<< EOF
			%images_list%
			%loading%
EOF;
	
		/*$images = fb_get_all_images(
			get_the_ID(),
			array('slideshow'),
			array(),
			false
		);*/
		
		$images = get_posts(
			array(
				'post_parent'	=> get_the_ID(),
				'post_type'		=> 'attachment',
				'tax_query' 	=>	array(
					'taxonomy'		=> 'media_tag',
					'field'			=> 'slug',
					'terms'			=> 'slideshow',
					'operator'		=> 'IN'
				)
			)
		);
		
		$this
			->add_images($images)
			//->set_timthumb_options(array('q'=>50,'w'=>1980,'h'=>1080))
			->add_asset('slideshow', 'js')
			->set_uid('slideshow')
			->set_markup(
				'loading',
				'<div class="loading" data-description="'
					.__('Loading image %number% of %total%', 'theme')
					.'"></div>'
			)
			->set_template($tpl);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ImagePreload::get_markup()
	 */
	function get_markup(){
		$images_list = parent::get_markup();
		$this->set_markup('images_list', $images_list);
		return $this->replace_markup();
	}
}



/**
 * Print the <head> inner content
 */
function the_head(){
	echo '<head>';
	$header = new HeadHelper();
	$header
		->set_title(fbseo_get_title())
		->set_meta_tag(
			array(
				'name'		=>	'description',
				'content'	=>	fbseo_get_metadescription()
			)
		)
		->the_head();
	wp_head();
	echo '</head>';
}

/**
 * Print the doctype
 */
function the_doctype(){
	echo ThemeHelpers::doctype('html5');
}

/**
 * Print the <html> opening tag
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

/**
 * Print the markup from the FBSeo plugin
 */
function the_fb_seo(){
	echo ThemeHelpers::heading();
}

/**
 * Tells the guest his browser is too old
 * and he should upgrade to browse this website
 * with his heart full of happiness
 */
function the_browse_happy(){
	echo ThemeHelpers::browse_happy();
}


/**
 * Prints some text and some links 
 * to get out of the 404 page not found.
 */
function the_404_escape_route(){
	$escape = new EscapeRoute();
	$escape->templates->set_markup('class', 'grid_6');
	echo $escape->get_markup();
}

function the_404_image(){
	echo ThemeHelpers::image(get_template_directory_uri().'/images/error_404.gif');
	//echo wp_get_attachment_image(4, 'two-columns');
}

function the_post_image(){
	$images = get_posts(
		array(
			'post_parent'	=>	get_the_ID(),
			'post_type'		=>	'attachment',
			'numberposts'	=>	-1,
			'exclude'		=>	get_post_thumbnail_id(),
			'orderby'		=>	'post_order',
			'order'			=>	'ASC',
			'tax_query' 	=>	array(
				'taxonomy'		=>	'media_tag',
				'field'			=>	'slug',
				'terms'			=>	'slideshow',
				'operator'		=>	'NOT IN'
			)
		)
	);
	
	//$images = get_attachments_by_media_tags('media_tags=slideshow');
	//$images = wp_get_attachment_url();
	if(count($images)>0){
		$good_sizes = true;
		foreach($images as $image){
			$metas = wp_get_attachment_metadata($image->ID);
			if(intval($metas['width']) < 460 && intval($metas['height']) < 250 ){
				$good_sizes = false; 
				break;
			}
		}
		
		if($good_sizes){
			$minigallery = new MinigalleryBigImageWithThumbs();
			echo $minigallery
				->add_images($images)
				->set_markup('next', '<div class="next">&raquo;</div>')
				->set_markup('prev', '<div class="next">&laquo;</div>')
				->set_markup('loading', '<div class="loading">'.__('Loading Image...', 'theme').'</div>')
				->get_markup();
		} else {
			foreach($images as $image){
				echo wp_get_attachment_image($image->ID);
			}
		}
	} else { 
		echo wp_get_attachment_image(4, 'two-columns'); 
	}
}

function the_post_head_image(){
	if(has_post_thumbnail()){
		the_post_thumbnail();
	}else{
		$image_found = false;
		global $post;
		while($post->post_parent!=0){
			$post = get_post($post->post_parent);
			setup_postdata($post);
			if(has_post_thumbnail()){
				the_post_thumbnail();
				$image_found = true;
			}
		}
		wp_reset_postdata();
		
		if(!$image_found){
			echo wp_get_attachment_image(75, 'post-thumbnail');
		}
	}
}

/**
 * Prints the content after the mode tag
 */
function the_content_after_more(){
	the_content(null, true);
}

/**
 * Prints the content before the more tag
 * If the tag is not present it will print the entire body
 */
function the_content_before_more(){
	global $more;
	$swap = $more; 
	$more = 0; 
	the_content('', false); 
	$more = $swap;
}

/**
 * Checks if the current post has the <!--more--> tag
 */
function has_more_tag(){
	global $post;
	return strpos($post->post_content, '<!--more-->')!==false;
}