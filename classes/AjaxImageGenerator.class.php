<?php 
/**
 * Contains AjaxImageGenerator class definition
 */


/**
 * Hooks the ImageGenerator class to admin-ajax action
 * 
 * @author etessore
 * @version 1.0.0
 * @package classes
 * 
 */
class AjaxImageGenerator{
	/**
	 * @var bool hook to WordPress only once
	 */
	private static $is_hooked;
	
	/**
	 * Sets the admin-ajax hooks
	 * @param bool $priv is the hook available for registered users?
	 * @param bool $nopriv is the hook available for the non-registered users?
	 */
	public function __construct($priv=true, $nopriv=true){
		if(!$this::$is_hooked){
			$this->init($priv, $nopriv);
		}
	}
	
	/**
	 * Sets the admin-ajax hooks
	 * @param bool $priv is the hook available for registered users?
	 * @param bool $nopriv is the hook available for the non-registered users?
	 */
	private function init($priv=true, $nopriv=true){
		if($priv){
			add_action('wp_ajax_placeholder', array(__CLASS__, 'ajax_callback'));
		}
		if($nopriv) {
			add_action('wp_ajax_nopriv_placeholder', array(__CLASS__, 'ajax_callback'));
		}
	}
	
	/**
	 * This method is called by the admin-ajax subsystem.
	 * Prints the image. 
	 */
	public static function ajax_callback(){
		$image = new ImageGenerator();
		
		$image
			->set('width', intval($_REQUEST['w']))
			->set('height', intval($_REQUEST['h']))
			->set('bg_color', 'cccccc')
			->set('text_color', '222222')
			->flush(isset($_REQUEST['refresh']))
			->image();
	}
}