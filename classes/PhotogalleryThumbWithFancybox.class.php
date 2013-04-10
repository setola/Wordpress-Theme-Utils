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
	 * Initializes the photogallery
	 */
	public function __construct(){
		$this
		->set_markup('loading', '<div class="loading">'.__('Loading...', $this->textdomain).'</div>')
		->set_sizes(array('w'=>220,'h'=>150))
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
	 * Sets the images sizes: array('w'=>xxx, 'h'=>yyy)
	 * @param array $sizes
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
			$images_per_line = 4;
			foreach ($this->images as $k => $image){
				$classes = array('grid_4', 'image_'.$k);
				
				if($images_per_line == 0){
					$images_per_line = 4;
				}
				if($images_per_line == 4){
					$classes[] = 'alpha';
				}
				if($images_per_line == 1){
					$classes[] = 'omega';
				}
				$images_per_line--;
				
				
				$markup_images .= 
					'<div class="'.implode(' ', $classes).'">'.
					ThemeHelpers::anchor(
						$this->get_image_src($k), 
						ThemeHelpers::image(
							$this->get_image_src($k)
							.'?'
							.http_build_query($this->sizes)
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