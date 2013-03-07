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
		
		$this->add_css('reset', 'http://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.css', null, '2.0', 'screen');
		$this->add_css('grid-960', 'http://cdnjs.cloudflare.com/ajax/libs/960gs/0/960.css', null, '0.1', 'screen');
		return $this;
	}
}