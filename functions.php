<?php
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

//TODO: clean useless initializations

/**
 * Iframe system
 */
/*global $offers;
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
*/
/**
 * Runtime infos
 */
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







// TODO: move this into ThemeHelpers to allow overloading in child theme 





/**
 * Prints the sub pages
 */
/*
function the_children(){
	$children = get_pages(
			array(
					'child_of' 		=>	get_the_ID(),
					'sort_column' 	=>	'menu_order',
					'sort_order' 	=>	'desc'
			)
	);

	if(count($children)) :

	$subs = new SubstitutionTemplate();
	$subs->set_tpl(<<<EOF
	<div class="sub-item %column%-col" id="page_%item-id%">
		<div class="inner">
			<div class="title">%title%</div>
			<div class="body">%body%</div>
			<div class="more">%button%</div>
		</div>
	</div>
EOF
	);

	$columns = array('left', 'center', 'right');

	echo '<div id="sub-items" class="three-cols clearfix">';
	foreach($children as $k => $post){
		global $post;
		setup_postdata($post);
		echo $subs
		->set_markup('item-id', get_the_ID())
		->set_markup('title', 	ThemeHelpers::anchor(get_permalink(), get_the_title()))
		->set_markup('column', 	$columns[$k%3])
		->set_markup('body', 	get_the_excerpt())
		->set_markup('button', 	ThemeHelpers::anchor(get_permalink(), __('Details', 'theme')))
		->replace_markup();
		;
	}
	wp_reset_postdata();

	echo '</div>';
	endif;
}
*/


/**
 * Print the <html> opening tag
 * @param string|array $class some additional classes
 */
if(!function_exists('the_html')) { 
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

/**
 * Print the markup from the FBSeo plugin
 */
/*function the_fb_seo(){
	echo ThemeHelpers::heading();
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
*/
/**
 * Prints the logo
 */
/*function the_logo(){
	echo wp_get_attachment_image(LOGO_MEDIA_ID, 'logo');
}*/