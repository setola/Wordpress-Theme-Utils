<?php 
/**
 * The template part for displaying the slideshow
 * 
 * @since 0.1
 */

add_image_size('slideshow',940, 400);

/**
 * Manages the slideshow
 * @author etessore
 * @version 1.0.1
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
			->get_the_images()
			->set_wp_media_dimension('slideshow')
			->set_uid('slideshow');
	}

	/**
	 * Retrives the images for the slideshow
	 * @param int $post_id the post id to dig in
	 * @return Slideshow $this for chainability
	 */
	function get_the_images($post_id = null){
		$this->post_id = ($post_id) ? $post_id : get_the_ID();
		
		$args = array(
				'post_parent'		=> $this->post_id,
				'post_type'			=> 'attachment',
				'post_mime_type'	=> 'image',
		);
		
		if(taxonomy_exists('media_tag')){
			$args['tax_query'] 	=	array(
					'taxonomy'		=> 'media_tag',
					'field'			=> 'slug',
					'terms'			=> 'slideshow',
					'operator'		=> 'IN'
			);
		}

		$this->images = get_children($args);

		if(empty($this->images)){
			$args['post_parent'] = get_option('page_on_front');
			$this->images = get_children($args);
		}
		
		if(empty($this->images)){
			
			$dimensions = ImageGenerator::get_dimensions('slideshow');
			$image = new ImageGenerator();
			$image
				->set('width', $dimensions['width'])
				->set('height', $dimensions['height'])
				/*->set('image_text', 'Slideshow')
				->set('font_size', '150')
				->set('text_position_x', '50')
				->set('text_position_y', '250')*/
				->set('bg_color', 'cccccc')
				->set('text_color', '222222');
			
			$this->add_image(array(
				'src'	=>	$image->get_image_src(),
				'alt'	=>	$image->get_image_alt(),
				'width'	=>	$image->get_image_width(),
				'height'=>	$image->get_image_height()	
			));
		}

		return $this;
	}
}




ThemeHelpers::load_js('slideshow');
/**
 * Prints the slideshow
 */
$preloader = new Slideshow();
$preloader->the_markup();


