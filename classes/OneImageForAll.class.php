<?php 

/**
 * Merges all images of a gallery in only one and serve it as a sprite
 * Important: every image have the same sizes and jpeg quality.
 * @author etessore
 * @version 1.0.0
 */
class OneImageForAll extends GalleryHelper{
	public $cache_name;
	public $cache_dir;
	public $cache_url;
	public $cache_path;
	public $force_image_refresh;
	public $config;
	public $no_images;
	
	/**
	 * Initializes the object with default values
	 * @param array $this->config config for the images
	 */
	public function __construct(){
		$this->force_image_refresh = false;
		$this->no_images = 'No Images!';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
	public function get_markup(){
		$this
			->check_config()
			->cache_names()
			->merge_images();
		
		$tpl = new SubstitutionTemplate();
		$tpl->set_tpl('<div class="slideshow_image" title="%title%" style="%style%"></div>');
		$toret = '<div class="cycle" style="width:'.$this->config['w'].'px; height:'.$this->config['h'].'px; overflow:hidden;">';
		foreach($this->images as $index => $image){
			$toret .= $tpl
				->set_markup('title', $this->get_image_alt($index))
				->set_markup('style', $this->get_style($index))
				->replace_markup();
		}
		$toret .= '</div>';
		return $toret;
	}
	
	/**
	 * Calculates the style for the $image_id
	 * @param int $image_id the index of the image
	 * @return string the style html attribute
	 */
	public function get_style($image_id){
		$toret 	=	'background:url(\''.$this->cache_url.'\') no-repeat scroll -'.($this->config['w']*$image_id).'px 0 transparent; ';
		$toret	.=	'width:'.$this->config['w'].'px; ';
		$toret	.=	'height:'.$this->config['h'].'px; ';
		return $toret;
	}
	
	/**
	 * Checks if the config has all the paramaters needed.
	 * @return OneImageForAll $this for chainability
	 */
	public function check_config(){
		if(is_null($this->config)) 
			$this->config = array();
		
		$this->config = array_merge(
			$this->config, 
			array(
				'w'	=>	$this->get_image_width(0),
				'h'	=>	$this->get_image_height(0),
				'q'	=>	'80',
				'r'	=>	false
			)
		);
		
		
		return $this;
	}
	
	/**
	 * Calculates the cache name, dir, url and path.
	 * @return OneImageForAll $this for chainability
	 */
	public function cache_names(){
		
		$this->cache_name = '';
		foreach($this->images as $image){
			$this->cache_name .= $image->ID.';';
		}
		$this->cache_name .= serialize($this->config);
		$this->cache_name = md5($this->cache_name);
		
		$this->cache_dir = get_template_directory().'/cache/';
		if (!@is_dir($this->cache_dir)){
			if (!@mkdir($this->cache_dir)){
				die('Couldn\'t create cache dir: '.$this->cache_dir);
			}
		}
		$this->cache_url = get_bloginfo('template_url').'/cache/'.$this->cache_name.'.jpg';
		$this->cache_path = $this->cache_dir.$this->cache_name.'.jpg';
		
		return $this;
	}
	
	/**
	 * Merges the images in the current object and returns the url to the big image.
	 * @return OneImageForAll $this for chainability
	 */
	protected function merge_images(){
		if(empty($this->images)) 
			return $this->no_images;
		
		if(file_exists($this->cache_path) && $this->force_image_refresh!==true) 
			return $this;
		
		//vd($this->media_dimension);	
		$combined_image = imagecreatetruecolor(
			$this->config['w'] * count($this->images),
			$this->config['h']
		);
		
		//vd($this->config);
		
		foreach($this->images as $array_index => $image){
			$src = $this->get_image_src($array_index); 
			vc($src);
			//  $image['url'].'?'.http_build_query($this->config, '', '&');

			$info = getimagesize($src);
			switch($info['mime']){
				case 'image/jpeg':
					$image = imagecreatefromjpeg($src);
					break;
				case 'image/png':
					$image = imagecreatefrompng($src);
					break;
				case 'image/gif':
					$image = imagecreatefromgif($src);
					break;
				default:
					die('unknow mime type');
			}

			imagecopymerge(
				$combined_image,
				$image,
				$array_index*$this->config['w'],
				0, 0, 0,
				$this->config['w'],
				$this->config['h'],
				100
			);

			imagejpeg(
				$combined_image,
				$this->cache_path,
				$this->config['q']
			);
		}
		
		return $this;
	}
}