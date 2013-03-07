<?php 
/**
 * Maintains the code for DefaultAssetsCDN class
 */

/**
 * Registers some assets from some CDNs
 * 
 * @author etessore
 * @since 1.0
 * 
 */
class DefaultAssetsCDN extends DefaultAssets{
	/**
	 * (non-PHPdoc)
	 * @see DefaultAssets::register_standard()
	 */
	public function register_standard(){
		$this->add_js('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', null, '1.9.1', true);
		return $this;
	}
}