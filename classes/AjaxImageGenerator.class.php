<?php 

class AjaxImageGenerator{
	public function __construct(){
		add_action('wp_ajax_placeholder', array(__CLASS__, 'ajax_callback'));
		add_action('wp_ajax_nopriv_placeholder', array(__CLASS__, 'ajax_callback'));
	}
	
	public static function ajax_callback(){
		$image = new ImageGenerator();
		$image
			->set('width', intval($_REQUEST['w']))
			->set('height', intval($_REQUEST['h']))
			->set('bg_color', 'cccccc')
			->set('text_color', '222222');
	}
}