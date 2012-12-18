<?php 

class ImageGenerator{
	public $settings;
	
	private $image;
	private $cache;
	private $url;
	
	public function __construct(){}
	
	/**
	 * Initializes the object by checking 
	 * if the image exists or has to be generated
	 */
	public function init(){
		$path = wp_upload_dir();
		
		$this->cache['path']	=	$path['basedir'] . '/placeholders/';
		$this->cache['name']	=	md5(serialize($this->settings)).'.png';
		$this->cache['url']		=	$path['url'] . '/' . $this->cache['name'];
		
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
	 * Generates an image
	 * @return ImageGenerator $this for chainability
	 */
	private function generate_image(){
		$this->image = imagecreate($this->settings['width'], $this->settings['height']);
		
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
			$this->settings['width'] - 1, 
			$this->settings['height'] - 1, 
			$border
		);
		
		$fontsize = 
			($this->settings['width'] > $this->settings['height'])
			? ($this->settings['height'] / 10) 
			: ($this->settings['width'] / 10);
		
		
		imagettftext(
			$this->image,
			$fontsize, 
			0,
			($this->settings['width']/2) - ($fontsize * 2.75),
			($this->settings['height']/2) + ($fontsize* 0.2),
			imagecolorallocate($this->image, $text_color['red'], $text_color['green'], $text_color['blue']), 
			WORDPRESS_THEME_UTILS_PATH . '/fonts/DS-Digital.ttf', 
			$this->settings['width'].' X '.$this->settings['height']
		);
		
		return $this;
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
}