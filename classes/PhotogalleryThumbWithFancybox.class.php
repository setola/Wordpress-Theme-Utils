<?php 
/**
 * stores the PhotogalleryThumbWithFancybox class 
 */

/**
 * Manages images and markup for a photogallery where 
 * clicking on a little thumb opens the big image in fancybox 
 * 
 * It takes care of loading jQuery Fancybox if registered
 * @author etessore
 * @version 1.0.1
 * @package classes
 * 
*/

/*
 * Changelog
 * 	1.0.1
 *	 removed dependency from FeatureWithAssets, use ThemeHelpers::load_js() instead
 */
class PhotogalleryThumbWithFancybox extends GalleryHelper{
	
	/**
	 * @var array stores the sizes of the images
	 */
	public $sizes;
	
	/**
	 * The number of images per row
	 * @var int
	 */
	public $images_per_row;
	
	/**
	 * The image container class
	 * @var string
	 */
	public $single_image_container_class;
	
	/**
	 * Stores the html template
	 * @var SubstitutionTemplate
	 */
	public $subs;

	/**
	 * Initializes the photogallery
	 */
	public function __construct(){
		$this->subs = new SubstitutionTemplate();
		$this->subs
			->set_tpl(<<< EOF
	<div id="%gallery-id%" class="gallery">
		<div class="big-image-container">
			%images%
		</div>
	</div>
EOF
			)
			->set_markup('loading', '<div class="loading">'.__('Loading...', 'wtu_framework').'</div>');
		$this
			->set_wp_media_dimension('photogallery')
			->set_images_per_row(4)
			->set_single_image_container_class('grid_4');
	}
	
	/**
	 * Sets the number of images per row
	 * @param int $images_per_row a number
	 * @return PhotogalleryThumbWithFancybox $this for chainability
	 */
	public function set_images_per_row($images_per_row){
		$this->images_per_row = $images_per_row;
		return $this;
	}
	
	/**
	 * Set the class for single image container
	 * @param string $class the class
	 * @return PhotogalleryThumbWithFancybox $this for chainability
	 */
	public function set_single_image_container_class($class){
		$this->single_image_container_class = $class;
		return $this;
	}
	
	/**
	 * Sets the images sizes: array('w'=>xxx, 'h'=>yyy)
	 * @param array $sizes
	 * @return PhotogalleryThumbWithFancybox $this for chainability
	 */
	public function set_sizes($sizes){
		$this->sizes = $sizes;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
	public function get_markup(){
		$markup_images = '';
		if(count($this->images)>0){
			$images_per_line = $this->images_per_row;
			foreach ($this->images as $k => $image){
				$classes = array($this->single_image_container_class, 'image_'.$k);
				
				if($images_per_line == 0){
					$images_per_line = $this->images_per_row;
				}
				
				if($images_per_line == $this->images_per_row){
					$classes[] = 'alpha';
				}
				if($images_per_line == 1){
					$classes[] = 'omega';
				}
				$images_per_line--;
				
				$big_img_src = wp_get_attachment_image_src($this->get_image_id($k), 'full');
				$big_img_src = $big_img_src[0];
				
				
				$markup_images .= 
					'<div class="'.implode(' ', $classes).'">'.
					HtmlHelper::anchor(
						$big_img_src,
						HtmlHelper::image(
							$this->get_image_src($k),
							array(
								'alt'			=>	$this->get_image_alt($k)	
							)
						),
						array(
							'rel'				=>	'group', 
							'class'				=>	'fancy', 
							'title'				=>	$this->get_image_title($k),
							'data-description'	=>	$this->get_image_description($k),
							'data-caption'		=>	$this->get_image_caption($k)
						)
					)
					.'</div>';
			}
		}
	
		if(!$this->unid){
			$this->calculate_unique();
		}
		
		$this->subs->set_markup('gallery-id', $this->unid);
		$this->subs->set_markup('images', $markup_images);
		
		ThemeHelpers::load_js('jquery-fancybox');
		ThemeHelpers::load_css('jquery-fancybox');
		
		return $this->subs->replace_markup();
	}
}