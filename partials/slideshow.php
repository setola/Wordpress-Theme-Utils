<?php 
/**
 * The template part for displaying the slideshow
 * 
 * @version 1.0.0
 * @package templates
 * @subpackage parts
 * @since 0.1
 */
if(!array_key_exists('slideshow', ImageGenerator::get_all_image_sizes()))
	add_image_size('slideshow',940, 400);


?>
<div id="slideshow" class="slideshow">
<?php 
	ThemeHelpers::load_css('slideshow');
	ThemeHelpers::load_js('slideshow');
	/**
	 * Prints the slideshow
	 */
	$preloader = new ImagePreload();
	$preloader
		->get_images()
		->set_wp_media_dimension('slideshow')
		->set_uid('slideshow')
		->the_markup();
?>
</div>
<?php if($preloader->has_images()){
	get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'slideshow_controls');
} ?>