<?php 
/**
 * Manages the markup for the minigallery 
 * with a big image and some small thumbs
 * @author etessore
 * @version 1.0.0
 */
class MinigalleryBigImageWithThumbs{
	public $textdomain = 'theme';
	
	/**
	 * @var string the unique id for the current minigallery
	 * it will be automatically created if not specified
	 */
	public $unid = false;
	
	/**
	 * @var array stores the sizes of the images
	 */
	public $sizes;
	
	/**
	 * @var String the html template
	 */
	public $tpl;
	
	/**
	 * @var array the list of images
	 */
	public $images = array();
	
	/**
	 * @var array configuration for the thumbnails list
	 */
	public $thumb_list_config;
	
	/**
	 * @var array Stores some static html
	 */
	public $static_markup;
	
	public function __construct(){
		$this
			->set_big_sizes(460, 220)
			->set_small_sizes(90, 70)
			->set_thumbs_list_config(
				array(
					'number'	=>	4,
					'separator'	=>	array(
						'open'		=>	'<div class="thumb-set">',
						'close'		=>	'</div>'
					)
				)
			)
			->set_markup('loading', '<div class="loading">'.__('Loading...', $this->textdomain).'</div>')
			->set_markup('next', '<div class="next">'.__('Next', $this->textdomain).'</div>')
			->set_markup('prev', '<div class="next">'.__('Prev', $this->textdomain).'</div>')
			->set_markup('big-image', '<div class="big-image"></div>')
			->set_template(<<< EOF
	<div id="%minigallery-id%" class="minigallery">
		<div class="big-image-container">
			%big-image%
			%loading%
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
	public function set_big_sizes($widht, $height){
		$this->sizes['big']['w']	=	$widht;
		$this->sizes['big']['h']	=	$height;
		return $this;
	}
	
	/**
	 * Sets the sizes of the thumbs
	 * @param int $widht
	 * @param int $height
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function set_small_sizes($widht, $height){
		$this->sizes['small']['w']	=	$widht;
		$this->sizes['small']['h']	=	$height;
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
	 * Set the static markup; ie: prev\next\loading divs
	 * @param string $markup html markup
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function set_markup($key, $markup){
		$this->static_markup[$key] = $markup;
		return $this;
	}
	
	/**
	 * Adds an image to the current set
	 * @param string|int $img the image: if is an int it 
	 * will be retrieved from the wp media, elsewhere it is an html tag
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function add_image($img){
		$this->images[] = $img;
		return $this;
	}
	
	/**
	 * Add some images to the current set
	 * @param array $images the list of images to be added
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function add_images($images){
		foreach($images as $img){
			$this->add_image($img);
		}
		return $this;
	}
	
	/**
	 * Loads the needed assets
	 * @return MinigalleryBigImageWithThumbs $this for chainability
	 */
	public function load_assets(){
		wp_enqueue_style(
				'minigallery', 
				get_template_directory_uri().'/css/minigallery.css', 
				null, 
				'0.1', 
				'screen'
		);
		wp_enqueue_script(
				'minigallery', 
				get_template_directory_uri().'/js/minigallery.js', 
				array('jquery', 'jquery.cycle'), 
				'1.0.0', 
				true
		);
		
		
		return $this;
	}
	
	/**
	 * @return the markup for the mini gallery
	 */
	public function get_markup(){
		$toret = '';
		if(count($this->images)>0){
			$this->load_assets();
			if(!$this->unid) {
				$this->unid = uniqid('minigallery-');
			}
			
			$thumb_list = '';
			foreach ($this->images as $k => $image){
				if( $k % $this->thumb_list_config['number'] == 0 && $k > 0){
					$thumb_list .= 
						$this->thumb_list_config['separator']['close'] . 
						$this->thumb_list_config['separator']['open'];
				}
				
				$src = $image;
				
				if(is_integer($src)){
					$src = wp_get_attachment_url($src);
				}
				
				if(is_object($src)){
					$src = wp_get_attachment_url($src->ID);
				}
				
				$thumb_list .= ThemeHelpers::anchor(
					$src.'?'.http_build_query($this->sizes['big']),
					ThemeHelpers::image(
						$src.'?'.http_build_query ($this->sizes['small']), 
						'class="image"'
					),
					'class="thumb-link"'
				);
			}
		}
		
		$this->static_markup['minigallery-id'] = $this->unid;
		$this->static_markup['thumb-list'] = 
			$this->thumb_list_config['separator']['open']
			.$thumb_list
			.$this->thumb_list_config['separator']['close'];
		
		return str_replace(
			array_map(create_function('$k', 'return "%".$k."%";'), array_keys($this->static_markup)), 
			array_values($this->static_markup), 
			$this->tpl
		);
	}
	
}