<?php 

/**
 * Manages a set of info to be printed as a json generally at the endo of the page
 * @author etessore
 * @version 1.0.1
 * 
 * Changelog
 * 1.0.1
 * 	solved notice of undefined post on 404 pages
 */
class RuntimeInfos{
	public $infos = array();
	public $type;
	protected $id;
	
	const TYPE_FOOTER = 1;
	const TYPE_AJAX = 2;
	const ajax_action = 'runtime-infos';
	
	/**
	 * Initializes the current set of infos with default values
	 */
	public function __construct(){
		$this->set_type(self::TYPE_AJAX);
	}
	
	/**
	 * Sets the default values: 
	 * postID, theme_url, upload_dir, home_url and ajaxurl
	 */
	public function set_standard_infos(){
		$uploadDir = wp_upload_dir();
		$uploadDir = $uploadDir['baseurl'];
		$this
			->set_type(self::TYPE_AJAX)
			->add_info('postID', $this->get_the_ID())
			->add_info('theme_url', get_template_directory_uri())
			->add_info('upload_dir', $uploadDir)
			->add_info('home_url', get_bloginfo('url'))
			->add_info('ajaxurl', admin_url('admin-ajax.php'));
		
		if(defined('ICL_LANGUAGE_CODE')){
			$this->add_info('currentLanguage', substr(ICL_LANGUAGE_CODE, 0, 2));
		}
		
		$this->generate_unique_id();

		set_transient($this->id, $this->infos);
		if($this->type == self::TYPE_AJAX){
			$this->load_assets();
		}
	}
	
	/**
	 * Get the ID for the current post
	 */
	private function get_the_ID(){
		if(is_404()) return '';
		return get_the_ID();
	}
	
	/**
	 * Adds an info to the current set
	 * @param string $key the name of the info
	 * @param string $value the value of the info
	 * @return RuntimeInfos $this for chainability
	 */
	public function add_info($key, $value){
		$this->infos[$key] = $value;
		return $this;
	}
	
	public function set_type($type){
		$this->type = $type;
		return $this;
	}
	
	/**
	 * Hook the print of this infos to Wordpress footer
	 * @return RuntimeInfos $this for chainability
	 */
	public function hook(){
		switch($this->type){
			case self::TYPE_AJAX:
				add_action('wp_ajax_'.self::ajax_action, array(__CLASS__, 'ajax_callback'));
				add_action('wp_ajax_nopriv_'.self::ajax_action, array(__CLASS__, 'ajax_callback'));
				add_action('wp', array(&$this, 'set_standard_infos'));
			break;
			
			case self::TYPE_FOOTER:
				add_action('wp_footer', array(&$this, 'the_markup'));
			break;
		}
		return $this;
	}
	
	/**
	 * Registers and enqueue the js from admin-ajax.php
	 * This has to be called after RuntimeInfos::generate_unique_id()
	 */
	public function load_assets(){
		if(!isset($this->id)){
			wp_die('You have to call RuntimeInfos::generate_unique_id() before RuntimeInfos::load_assets()');
		}
		$nonce = wp_create_nonce("runtime_infos_nonce");
		$src = 
			admin_url('admin-ajax.php')
			.'?'
			.http_build_query(
				array(
					'action'	=>	self::ajax_action,
					'id'		=>	$this->id,
					'nonce'		=>	$nonce
				)
			);
		wp_register_script('ajax-runtime-infos', $src, false, false, false);
		wp_enqueue_script('ajax-runtime-infos');
	}
	
	/**
	 * Format the data set in JSON.
	 * Overload this if you want a different
	 * data structure, i.e. XML
	 */
	protected function render(){
		return json_encode($this->infos);
	}
	
	/**
	 * Retrieves the html markup
	 * @return string html markup
	 */
	function get_markup() {
		return ThemeHelpers::script($this->render(), 'id="runtime-infos"');
	}
	
	/**
	 * Generates an unique ID for the current data set
	 * @return string the ID
	 */
	private function generate_unique_id(){
		$this->id = abs(crc32(serialize($this->infos)));
		return $this->id;
	}
	
	/**
	 * Prints the markup
	 */
	public function the_markup(){
		echo $this->get_markup();
	}
	
	/**
	 * Callback executed when the admin ajax url 
	 * is called with self::ajax_action parameter
	 */
	public static function ajax_callback(){
		$data = get_transient(intval($_GET['id']));
		header("Content-type: text/javascript");
		die('var runtimeInfos='.json_encode($data).';');
	}
}