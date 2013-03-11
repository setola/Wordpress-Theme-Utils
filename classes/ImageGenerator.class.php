<?php 
/**
 * stores the ImageGenerator class definition
 */
/**
 * Generates paceholder images
 * @author etessore
 * @version 1.0.1
 * 
 * 1.0.1
 * 	added hooks into wordpress
 * 1.0.0
 * 	Initial release
 */
class ImageGenerator{
	/**
	 * @var array stores the settings for this generator
	 */
	public $settings;
	
	/**
	 * @var resource an image resource identifier
	 */
	private $image;
	
	/**
	 * @var array some infos about the cache: url\path\filename...
	 */
	private $cache;
	
	/**
	 * @var string url for the generated image
	 */
	private $url;
	
	
	/**
	 * Initializes the object by checking 
	 * if the image exists or has to be generated
	 */
	public function init(){
		$path = wp_upload_dir();
		
		$subdir = '/placeholders/';
		
		$this->cache['path']	=	$path['basedir'] . $subdir;
		$this->cache['name']	=	md5(serialize($this->settings)).'.png';
		$this->cache['url']		=	$path['baseurl'] . $subdir . $this->cache['name'];
		
		if(!is_dir($this->cache['path'])){
			mkdir($this->cache['path']);
		}
		
		if(!file_exists($this->cache['path'].$this->cache['name']) && empty($this->image)) {
			$this->generate_image()->save_image();
		}
		
		return $this;
	}
	
	/**
	 * Sets a parameter for the current generator
	 * @param string $param the name of the parameter
	 * @param string $value the value of the parameter
	 * @return ImageGenerator $this for chainability
	 */
	public function set($param, $value){
		$this->settings[$param] = $value;
		return $this;
	}
	
	/**
	 * Retrieves the html markup for the current generated image
	 * @return the html markup for the current generated image
	 */
	public function get_markup(){
		$this->init();
		return HtmlHelper::image(
			$this->cache['url'], 
			array(
				'width'		=>	$this->settings['width'],
				'height'	=>	$this->settings['height'],
				'alt'		=>	'Placeholder image '.$this->settings['width'].'x'.$this->settings['height']
			)
		);
	}
	
	/**
	 * Retrieves the image src attribute
	 */
	public function get_image_src(){
		$this->init();
		return $this->cache['url'];
	}
	
	/**
	 * Generates an image
	 * @return ImageGenerator $this for chainability
	 */
	private function generate_image(){
		$this->image = imagecreate($this->get_image_width(), $this->get_image_height());
		
		$bg_color = self::get_rgb($this->settings['bg_color']);
		$text_color = self::get_rgb($this->settings['text_color']);
		
				
		imagefill(
			$this->image, 
			0, 
			0, 
			imagecolorallocate(
				$this->image, 
				$bg_color['red'], 
				$bg_color['green'], 
				$bg_color['blue']
			)
		);
		
		$border = imagecolorallocate(
			$this->image, 
			$text_color['red'], 
			$text_color['green'], 
			$text_color['blue']
		);
		imagerectangle(
			$this->image, 
			0, 0, 
			$this->get_image_width() - 1, 
			$this->get_image_height() - 1, 
			$border
		);
		
		$fontsize = $this->settings['font_size'];
		if(empty($fontsize) || !is_numeric($fontsize)) $fontsize = 
			($this->settings['width'] > $this->settings['height'])
			? ($this->get_image_height() / 10) 
			: ($this->get_image_width() / 10);
		
		$image_text = (isset($this->settings['image_text']))
			? $this->settings['image_text']
			: $this->get_image_width().' X '.$this->get_image_height();
		
		$text_position_x = $this->settings['text_position_x'];
		if(empty($text_position_x) || !is_numeric($text_position_x))
			$text_position_x = ($this->get_image_width()/2) - ($fontsize * 2.75);
		
		$text_position_y = $this->settings['text_position_y'];
		if(empty($text_position_y) || !is_numeric($text_position_y))
			$text_position_y = ($this->get_image_height()/2) + ($fontsize* 0.2);
		
		imagettftext(
			$this->image,
			$fontsize, 
			0,
			$text_position_x,
			$text_position_y,
			imagecolorallocate($this->image, $text_color['red'], $text_color['green'], $text_color['blue']), 
			WORDPRESS_THEME_UTILS_PATH . '/fonts/DS-Digital.ttf', 
			$image_text
		);
		
		return $this;
	}
	
	/**
	 * Gets the image width
	 */
	public function get_image_width(){
		return $this->settings['width'];
	}
	
	/**
	 * Get the image height
	 */
	public function get_image_height(){
		return $this->settings['height'];
	}
	
	/**
	 * Generates an alternative text for this image
	 */
	public function get_image_alt(){
		return 'Placeholder image '.$this->get_image_width().'x'.$this->get_image_height();
	}
	
	/**
	 * Saves the image into the upload directory
	 * @return ImageGenerator $this for chainability
	 */
	private function save_image(){
		$this->init();
		imagepng($this->image, $this->cache['path'].'/'.$this->cache['name']);
		return $this;
	}
	
	/**
	 * Flush the cache file and regenerate it
	 * @param bool $check false only if you don't want to delete the cache file.
	 * Useful to pass isset($_REQUEST['refresh']) in the ajax implementation
	 * @return ImageGenerator $this for chainability
	 */
	public function flush($check=true){
		if($check){
			$this->init();
			@unlink($this->cache['path'].'/'.$this->cache['name']);
			$this->init();
		}
		return $this;
	}
	
	/**
	 * Hooks into WordPress to serve a generated image when no one is available
	 */
	public function hook(){
		get_the_post_thumbnail();
		add_filter( "get_post_metadata", array($this, 'get_post_metadata_callback'), 10, 4);
		add_action( "post_thumbnail_html", array($this, 'post_thumbnail_html_callback'), 10, 5);
	}
	
	/**
	 * Callback for 'get_post_metadata' hook.
	 * Returns -1 if post image is not set, else the id of the image
	 * @param int $value the ID of the post thumbnail
	 * @param int $object_id ID of the object metadata is for
	 * @param string $meta_key Optional.  Metadata key.  
	 * 		If not specified, retrieve all metadata for the specified object.
	 * @param bool $single Optional, default is false.  
	 * 		If true, return only the first value of the specified meta_key.  
 	 * 		This parameter has no effect if meta_key is not specified.
	 */
	public function get_post_metadata_callback($value, $object_id, $meta_key, $single){
		if($meta_key=='_thumbnail_id' && is_null($value)){
			return -1;
		}
		
		return $object_id;
	}
	
	/**
	 * Callback for 'post_thumbnail_html' hook.
	 * Changes the html markup if the post thumbnail is not set
	 * @param string $html
	 * @param unknown_type $post_id
	 * @param unknown_type $post_thumbnail_id
	 * @param unknown_type $size
	 * @param unknown_type $attr
	 */
	public function post_thumbnail_html_callback($html, $post_id, $post_thumbnail_id, $size, $attr){
		if($post_thumbnail_id==-1){
			$sizes = self::get_all_image_sizes();
			if(empty($size) || !in_array($size, array_keys($sizes))){
				$size = 'medium';
			}
			return HtmlHelper::image(
				admin_url('admin-ajax.php')
				.'?'.build_query(array(
					'action'	=>	'placeholder',
					'w'			=>	$sizes[$size]['width'],
					'h'			=>	$sizes[$size]['height']
				),
				array(
					'alt'		=>	__('Placeholder post thumbnail'),
					'width'		=>	$sizes[$size]['width'],
					'height'	=>	$sizes[$size]['height']		
				))
			);
		}
		return $html;
	}
	
	/**
	 * Finds width and height of a media size
	 * @param string $media_dimensions the media size
	 * @return array array('width'=>'','height'=>'','crop'=>'')
	 */
	public static function get_dimensions($media_dimensions){
		$sizes = self::get_all_image_sizes();
		if(isset($sizes[$media_dimensions]))
			return $sizes[$media_dimensions];
		
		return array('width'=>'unknown','height'=>'unknown','crop'=>'unknown');;
	}
	
	/**
	 * outputs the raw image with appropriate header.
	 * @return ImageGenerator $this for chainability
	 */
	public function image(){
		$this->init();
		header("Content-Type: image/png");
		if(empty($this->image)){
			echo file_get_contents($this->cache['path'].'/'.$this->cache['name']);
		} else {
			imagepng($this->image);
		}
		return $this;
	}
	
	/**
	 * Retrieves the rgb value from the given hexadecimal
	 * @param string $hex the hex representation of the color
	 * @return array red green and blue values
	 */
	public static function get_rgb($hex){
		return array(
			'red'	=>	base_convert(substr($hex, 0, 2), 16, 10),
			'green'	=>	base_convert(substr($hex, 2, 2), 16, 10),
			'blue'	=>	base_convert(substr($hex, 4, 2), 16, 10),
		);
	}
	
	/**
	 * Retrieves all the image sizes registered in WordPress
	 * @return array of all image sizes registered in WP
	 * <code>'size_name' => array(
	 * 	'width'		=>	200,
	 * 	'height'	=>	80,
	 * 	'crop'		=>	true
	 * )
	 */
	public static function get_all_image_sizes(){
		global $_wp_additional_image_sizes;
		return array_merge($_wp_additional_image_sizes, array(
			'thumbnail'	=>	array(
				'width'		=>	get_option('thumbnail_size_w'),
				'height'	=>	get_option('thumbnail_size_h'),
				'crop'		=>	get_option('thumbnail_crop')
			),
			'medium'	=>	array(
				'width'		=>	get_option('medium_size_w'),
				'height'	=>	get_option('medium_size_h'),
				'crop'		=>	false	
			),
			'large'	=>	array(
				'width'		=>	get_option('large_size_w'),
				'height'	=>	get_option('large_size_h'),
				'crop'		=>	false	
			),
		));
	}
}