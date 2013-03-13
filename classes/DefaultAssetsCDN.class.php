<?php 
/**
 * Maintains the code for DefaultAssetsCDN class
 */

/**
 * Registers some assets from public available CDNs:
 * ajax.googleapis.com
 * cdnjs.cloudflare.com
 * cdn.jsdelivr.net
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
		
		// some js libraries
		$this->add_js('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', null, '1.9.1', true);
		
		$this->add_js('jquery.imagesloaded', 'http://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/2.1.0/jquery.imagesloaded.min.js', array('jquery'), '2.1.0', true);
		$this->add_js('jquery.cycle', 'http://cdnjs.cloudflare.com/ajax/libs/jquery.cycle/2.9999.8/jquery.cycle.all.min.js', array('jquery'), '2.9999.8', true);
		$this->add_js('jquery.scrollto', 'http://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.3/jquery.scrollTo.min.js', array('jquery'), '1.4.3', true);
		$this->add_js('jquery-fancybox', 'http://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.4/jquery.fancybox.pack.js', array('jquery'), '2.1.4', true);
		$this->add_js('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/jquery-ui.min.js', array('jquery'), '1.10.1', true);
		
		//Fblib, remember to fill the fbcallback.js in your child theme!
		$this->add_js('fbqs', 'http://static.fbwebprogram.com/fbcdn/fastqs/fastbooking_loader.php?v=1&callbackScriptURL='
				.get_stylesheet_directory_uri().'/js/fbcallback.js', array('jquery', 'jquery-ui'), '1', true);
		
		
		// some useful css
		$this->add_css('reset', 'http://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.css', null, '2.0', 'screen');
		$this->add_css('grid-960', 'http://cdnjs.cloudflare.com/ajax/libs/960gs/0/960.css', null, '0.1', 'screen');
		
		$this->add_css('jquery-fancybox', '/css/jquery.fancybox_cdn.css', null, '2.1.0', 'screen');
		
		return $this;
	}
}