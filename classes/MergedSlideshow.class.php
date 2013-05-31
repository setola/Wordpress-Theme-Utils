<?php 
/**
 * Merge all the images into a big one and serve it as div background
 * 
 * Takes a list of n images with the same dimensions
 * and merges them in a unique image with sizes (n x width) and height.
 * Than renders an html dom to serve the big image as background of some divs
 * so that you can cycle it.
 * 
 * @author etessore
 * @version 1.0.0
 *
 */

class MergedSlideshow extends OneImageForAll{
	function __construct(){
		parent::__construct();

		$tpl = (is_front_page() || has_post_thumbnail())
		? <<< EOF
			<div class="images_list">%images_list%</div>
			%loading%
EOF
: <<< EOF
			<div class="images_list">%images_list%</div>
			%loading%
			%next%
			%prev%
EOF;
		//$this->force_image_refresh = true;
		$this
			->set_wp_media_dimension($this->get_slideshow_dimension())
			->get_the_images()
			->set_uid('slideshow')
			->set_markup(
					'loading',
					'<div class="loading" data-description="'
					.__('Loading image %number% of %total%', 'theme')
					.'"></div>'
			)
			->set_markup('next', '<div id="go-next" class="sprite next"></div>')
			->set_markup('prev', '<div id="go-prev" class="sprite prev"></div>')
			->set_template($tpl);
	}

	/**
	 * @return string the media gallery size for the slideshow
	 */
	function get_slideshow_dimension(){
		global $post;
		$toret = 'slideshow';
		if(get_post_meta( $post->ID, '_wp_page_template', true ) == 'offers.php')
			$toret .= '-small';
		if(is_front_page())
			$toret .= '-home';

		return $toret;
	}


	/**
	 * Retrives the images for the slideshow
	 * @param int $post_id the post id to dig in
	 * @return Slideshow $this for chainability
	 */
	function get_the_images($post_id = null){

		$this->post_id = ($post_id) ? $post_id : get_the_ID();
		$this->images = get_posts(
				array(
						'post_parent'	=> $this->post_id,
						'post_type'		=> 'attachment',
						'post_mime_type'=> 'image',
						'orderby'		=> 'menu_order',
						'order'			=> 'ASC',
						'tax_query' 	=>	array(
								'taxonomy'		=> 'media_tag',
								'field'			=> 'slug',
								'terms'			=> 'slideshow',
								'operator'		=> 'IN'
						)
				)
		);

		if(empty($this->images)){
			$this->images = get_posts(
					array(
							'post_parent'	=> get_option('page_on_front'),
							'post_type'		=> 'attachment',
							'tax_query' 	=>	array(
									'taxonomy'		=> 'media_tag',
									'field'			=> 'slug',
									'terms'			=> 'slideshow',
									'operator'		=> 'IN'
							)
					)
			);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see ImagePreload::get_markup()
	 */
	function get_markup(){
		ThemeHelpers::load_js('slideshow-oneimageforall');
		$images_list =
			(has_post_thumbnail())
			? get_the_post_thumbnail(get_the_ID(), $this->get_slideshow_dimension())
			: parent::get_markup();

		$this->set_markup('images_list', $images_list);
		return $this->replace_markup();
	}
}