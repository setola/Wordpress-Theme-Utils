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

/**
 * Manages the slideshow
 * @author etessore
 * @version 1.0.1
 * @package templates
 * @subpackage parts
 */
class Slideshow extends ImagePreload{
	/**
	 * @var int the id of the post
	 */
	public $post_id;
	
	/**
	 * @var array the list of imagese to be slided
	 */
	public $images;

	/**
	 * Initializes the parent object to the default values
	 */
	function __construct(){
		$this
			->get_images()
			->set_wp_media_dimension('slideshow')
			->set_uid('slideshow');
	}
}

?>
<div id="slideshow" class="slideshow">
<?php 
	ThemeHelpers::load_css('slideshow');
	ThemeHelpers::load_js('slideshow');
	/**
	 * Prints the slideshow
	 */
	$preloader = new Slideshow();
	$preloader->the_markup();
?>
</div>
