<?php 

class ImagePreload{
	const textdomain = 'theme';
	var $images;
	var $mod;
	var $loading_tpl;
	var $timthumb_parms;
	
	public function __construct($postID=null){
		$this
			->set_images(wp_get_imagelist_for_slideshow($postID))
			->set_mod('_default')
			->set_timthumb_parms('')
			->set_loading_tpl('Loading %number% of %lenght%');
	}
	
	/**
	 * Set the images
	 * @param array $images
	 * @return ImagePreload $this for chainability
	 */
	public function set_images(array $images){
		$this->images = $images;
		return $this;
	}
	
	/**
	 * Set the timthumb parameter
	 * @param string $parms timthumb parameter
	 * @return ImagePreload $this for chainability
	 */
	public function set_timthumb_parms($parms){
		$this->timthumb_parms = '?'.(trim($parms, '?'));
		return $this;
	}
	
	/**
	 * Set the loading template
	 * @param string $tpl
	 * @return ImagePreload $this for chainability
	 */
	public function set_loading_tpl($loading_tpl){
		$this->loading_tpl = $loading_tpl;
		return $this;
	}
	
	/**
	 * Set the modificator
	 * @param string $mod
	 * @return ImagePreload $this for chainability
	 */
	public function set_mod($mod){
		$this->mod = '_'.trim($mod, '_');
		return $this;
	}
	
	/**
	 * Loads needed scripts and css
	 */
	public function load_assets(){
		wp_enqueue_script(
				'jquery.imagesloaded', 
				get_template_directory_uri().'/js/jquery.imagesloaded.js', 
				array('jquery'), 
				'2.0.1', 
				true
		);
		wp_enqueue_script(
				'jquery.cycle', 
				get_template_directory_uri().'/js/jquery.cycle.js', 
				array('jquery'), 
				'2.0.1', 
				true
		);
		wp_enqueue_script(
				'cycle', 
				get_template_directory_uri().'/js/cycle.js', 
				array('jquery.imagesloaded', 'jquery', 'jquery.cycle'), 
				'0.1', 
				true
		);
	}
	
	/**
	 * Initializes the javascript array 
	 * with the list of images to be preloaded
	 * @param string $mod a modificator for the current 
	 * javascript array. Useful if you have more than one slideshow
	 */
	public function script(){
		if(empty($this->images)){ return ''; }
		
		$this->load_assets();
		$toret = "var loading_label{$this->mod} = ".json_encode($this->loading_tpl).";";
		$toret .= "var preload_images{$this->mod} = preload_images{$this->mod} || [];\n";
		
		foreach($this->images as $image){
			$image['url'] .= $this->timthumb_parms;
			$toret .= "preload_images{$this->mod}.push(".json_encode($image).");\n";
		}
		
		return "<script>\n".$toret."</script>\n";
	}
	
	/**
	 * Retrieves the markup for the 'Loading...' placeholder
	 */
	public function placeholder(){
		//$label = __('Loading...', self::textdomain);
		return <<< EOF
	<div class="loading"></div>
EOF;
	}
}