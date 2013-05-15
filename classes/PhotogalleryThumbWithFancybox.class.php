<?php 
/**
 * stores the PhotogalleryThumbWithFancybox class 
 */

/**
 * Manages images and markup for a photogallery where 
 * clicking on a little thumb opens the big image in fancybox 
 * @author etessore
 * @version 1.0.0
 * @package classes
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
	 * Initializes the photogallery
	 */
	public function __construct(){
		$this
			->set_markup('loading', '<div class="loading">'.__('Loading...', $this->textdomain).'</div>')
			->set_wp_media_dimension('photogallery')
			->set_images_per_row(4)
			->set_single_image_container_class('grid_4')
			->set_template(<<< EOF
	<div id="%gallery-id%" class="gallery">
		<div class="big-image-container">
			%images%
		</div>
	</div>
EOF
		);
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
	 * @see FeatureWithAssets::load_assets()
	 */
	public function load_assets(){
		wp_enqueue_style('photogallery');
		wp_enqueue_script('photogallery');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
	public function get_markup(){
		$markup_images = '';
		if(count($this->images)>0){
			$this->load_assets();
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
					ThemeHelpers::anchor(
						$big_img_src,
						ThemeHelpers::image(
							$this->get_image_src($k)
						),
						'rel="group" class="fancy"'
					)
					.'</div>';
			}
		}
	
		if(!$this->unid){
			$this->calculate_unique();
		}
	
		$this->static_markup['gallery-id'] = $this->unid;
		$this->static_markup['images'] = $markup_images;
	
		return str_replace(
				array_map(create_function('$k', 'return "%".$k."%";'), array_keys($this->static_markup)),
				array_values($this->static_markup),
				$this->tpl
		);
	}
}