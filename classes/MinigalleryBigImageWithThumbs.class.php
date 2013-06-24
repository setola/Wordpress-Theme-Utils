<?php 
/**
 * Stores the MinigalleryBigImageWithThumbs class definition
 */
/**
 * Manages the markup for the minigallery 
 * with a big image and some small thumbs
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class MinigalleryBigImageWithThumbs extends GalleryHelper{
	/**
	 * @var array stores the sizes of the images
	 */
	public $sizes;
	
	/**
	 * @var array configuration for the thumbnails list
	 */
	public $thumb_list_config;
	
	/**
	 * Initializes the Minigallery with the default config
	 */
	public function __construct(){
		$this
			->set_big_size('minigallery_bigsize')
			->set_small_size('thumbnail')
			->set_thumbs_list_config(
				array(
					'number'	=>	4,
					'separator'	=>	array(
						'open'		=>	'<div class="thumb-set">',
						'close'		=>	'</div>'
					)
				)
			)
			->set_markup('loading', '<div class="loading">'.__('Loading...', 'wtu_framework').'</div>')
			->set_markup('next', '<div class="next">'.__('Next', 'wtu_framework').'</div>')
			->set_markup('prev', '<div class="prev">'.__('Prev', 'wtu_framework').'</div>')
			->set_markup('big-image', '<div class="big-image"></div>')
			->set_markup('caption', '<div class="caption"></div>')
			->set_template(<<< EOF
	<div id="%minigallery-id%" class="minigallery">
		<div class="big-image-container">
			%big-image%
			%loading%
			%caption%
		</div>
		<div class="thumbs">
			%prev%
			<div class="thumbs-list">%thumb-list%</div>
			%next%
		</div>
	</div>
EOF
			);
	}
	
	/**
	 * Set the html template for this minigallery
	 * @param string $tpl the template
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function set_template($tpl){
		$this->tpl = $tpl;
		return $this;
	}
	
	/**
	 * Sets the sizes of the big image
	 * @param int $widht
	 * @param int $height
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function set_big_size($name){
		// $this->sizes['big']['w']	=	$widht;
		// $this->sizes['big']['h']	=	$height;
		$this->sizes['big'] = $name;
		return $this;
	}
	
	/**
	 * Sets the sizes of the thumbs
	 * @param int $widht
	 * @param int $height
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function set_small_size($name){
		// $this->sizes['small']['w']	=	$widht;
		// $this->sizes['small']['h']	=	$height;
		$this->sizes['small'] = $name;
		return $this;
	}
	
	/**
	 * Sets the config for the thumbnails list
	 * @param array $config the config array
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function set_thumbs_list_config($config=array()){
		$this->thumb_list_config = $config;
		return $this;
	}
	
	/**
	 * Loads the needed assets
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function load_assets(){
		ThemeHelpers::load_js('minigallery-big-image-with-thumbs');
		ThemeHelpers::load_css('minigallery-big-image-with-thumbs');
		return $this;
	}
	
	/**
	 * Retrieves the markup for this minigallery
	 * @return the markup for the minigallery
	 */
	public function get_markup(){
		$toret = '';
		if(count($this->images)>0){
			$this->load_assets();
			
			$thumb_list = '';
			foreach ($this->images as $k => $image){
				if( $k % $this->thumb_list_config['number'] == 0 && $k > 0){
					$thumb_list .= 
						$this->thumb_list_config['separator']['close'] . 
						$this->thumb_list_config['separator']['open'];
				}
				
				// $src = $this->get_image_src($k);
				$big_img_link = wp_get_attachment_image_src($this->get_image_id($k),$this->sizes['big']);
				// vd($big_img_link);
				$small_img_link = wp_get_attachment_image_src($this->get_image_id($k),$this->sizes['small']);
			 
				
				$thumb_list .= HtmlHelper::anchor(					
					$big_img_link[0],
					HtmlHelper::image(						
						$small_img_link[0],
						array('class'=>'image')
					),
					array(
						'class' 		=>	'thumb-link',
						'data-caption'	=>	$this->get_image_caption($k)
					)
				);
			}
			if(!$this->unid) {
				$this->calculate_unique();
			}

			$this->static_markup['minigallery-id'] = $this->unid;
			$this->static_markup['thumb-list'] =
			$this->thumb_list_config['separator']['open']
			.$thumb_list
			.$this->thumb_list_config['separator']['close'];

			$toret = str_replace(
					array_map(create_function('$k', 'return "%".$k."%";'), array_keys($this->static_markup)),
					array_values($this->static_markup),
					$this->tpl
			);
		}
		return $toret;
	}
	
}