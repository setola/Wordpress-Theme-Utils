<?php 

class SpecialOffersSnippet {
	const baseurl = 'http://hotelsitecontents.fastbooking.com/promotions.php';
	
	private $params;
	private $url;
	private $assets = array('js', 'css');

	public function __construct($hid){
		$this
			->add_param('hid', $hid)
			->add_param('nb', 1)
			->add_param('order', 'random');
		if(!is_admin() && $this->load_assets());
	}
	
	/**
	 * Enqueues the needed js and css 
	 * to the wordpress assets queue
	 * @return SpecialOffersSnippet $this for chainability
	 */
	public function load_assets(){
		wp_enqueue_script(
			'snippet-com', 
			get_template_directory_uri().'/js/com.js',
			array('jquery'),
			'0.1',
			true
		);
		wp_enqueue_script(
			'snippet-fbso', 
			'http://static.fbwebprogram.com/fbcdn/jquery_plugins/fbspecialoffers/1.1/jquery.fbspecialoffers.js',
			array('jquery'),
			'1.1',
			true
		);
		wp_enqueue_style(
			'snippet', 
			get_template_directory_uri().'/css/snippet.css', 
			null, 
			'0.1', 
			'screen'
		);
		return $this;
	}

	/**
	 * Builds the url for the remote iframe
	 * @param array $params additional query params for the iframe
	 */
	public static function calculate_url($params=array()){
		$url_arr = parse_url(self::baseurl);
		$url_arr['query'] = http_build_query($params);
		return self::build_url($url_arr);
	}
	
	/**
	 * Retrieves the url from array $url_arr
	 * If defined use http_build_url, else custom code
	 * @param array $url_arr the array of attributes 
	 * @see http://php.net/manual/en/function.parse-url.php
	 */
	private static function build_url($url_arr){
		if(function_exists('http_build_url')){
			return  http_build_url($url_arr);
		} else {
			$scheme   = isset($url_arr['scheme']) ? $url_arr['scheme'] . '://' : '';
			$host     = isset($url_arr['host']) ? $url_arr['host'] : '';
			$port     = isset($url_arr['port']) ? ':' . $url_arr['port'] : '';
			$user     = isset($url_arr['user']) ? $url_arr['user'] : '';
			$pass     = isset($url_arr['pass']) ? ':' . $url_arr['pass']  : '';
			$pass     = ($user || $pass) ? "$pass@" : '';
			$path     = isset($url_arr['path']) ? $url_arr['path'] : '';
			$query    = isset($url_arr['query']) ? '?' . http_build_query($params) : '';
			$fragment = isset($url_arr['fragment']) ? '#' . $url_arr['fragment'] : '';
			return  "$scheme$user$pass$host$port$path$query$fragment";
		}
	}

	/**
	 * Adds a new parameter
	 * @param string $key
	 * @param int|string $value
	 * @return SpecialOffersSnippet $this for chainability
	 */
	public function add_param($key, $value){
		$this->params[$key] = $value;
		return $this;
	}
	
	/**
	 * Removes a param
	 * @param string $key the param to be deleted
	 * @return SpecialOffersSnippet $this for chainability
	 */
	public function del_param($key){
		unset($this->params[$key]);
		return $this;
	}

	/**
	 * Retrieves the src of the iframe
	 * @return string the url
	 */
	public function get_iframe_src(){
		$url_arr = parse_url(admin_url('admin-ajax.php'));
		$this->add_param('action', SpecialOffersSnippetAjax::ajax_action);
		$url_arr['query'] = http_build_query($this->params);
		return self::build_url($url_arr);
	}
	
	/**
	 * Prints the iframe src
	 */
	public function the_iframe_src(){
		print($this->get_iframe_src());
		return $this;
	}


}



