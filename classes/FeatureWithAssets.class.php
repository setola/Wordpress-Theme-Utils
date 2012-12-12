<?php 
/**
 * Manages the assets loading for a feature.
 * 
 * WARNING: this method doesn't register the asset:
 * You have to take care of it by yourself (tipically in assets.php)
 * @author etessore
 * @version 1.0.1
 * 
 * 1.0.1
 * 	Fixed Notice: wp_enqueue_script was called incorrectly
 * 1.0.0
 * 	Initial release
 */
class FeatureWithAssets{
	
	/**
	 * @var array the list of assets to be loaded array('js'=>array(...), 'css'=>array(...));
	 */
	public $assets = array('js' => array(), 'css' => array());
	
	
	/**
	 * Set the assets list to the given one
	 * @param array $assets the list of assets: array('js'=>array(...), 'css'=>array(...));
	 * @return FeatureWithAssets $this for chainability
	 */
	public function set_assets($assets){
		if(
				is_array($assets)
				&& is_array($assets['js'])
				&& is_array($assets['css'])
		){
			$this->assets = $assets;
		}
		return $this;
	}
	
	/**
	 * Adds an asset. You cannot define an asset here,
	 * to do it please use wp_register_script or wp_register_style
	 * in functions.php or config/assets.php
	 * WARNING: this method doesn't register the asset:
	 * You have to take care of it by yourself (tipically in assets.php)
	 * @param string $handle the handle for the asset.
	 * @param string $type the asset type: js|css
	 * @return FeatureWithAssets $this for chainability
	 */
	public function add_asset($handle, $type){
		if($type == 'js' || $type == 'css'){
			$this->assets[$type][] = $handle;
		}
		return $this;
	}
	/**
	 * Loads needed scripts and css
	 * @return FeatureWithAssets $this for chainability
	 */
	public function load_assets(){
		if(!is_admin()){
			add_action('wp_enqueue_scripts', array($this, 'load_assets_callback'));
		}
		return $this;
	}
	
	/**
	 * Loads needed scripts and css
	 * @return FeatureWithAssets $this for chainability
	 */
	public function load_assets_callback(){
		foreach($this->assets['js'] as $js){
			wp_enqueue_script($js);
		}
		foreach($this->assets['css'] as $css){
			wp_enqueue_script($css);
		}
		return $this;
	}
}