<?php 
/**
 * Manages the integration of the special offers snippet
 * @author etessore
 * @version 1.0.2
 * 
 * 1.0.2
 * 	Fixed Notice: wp_enqueue_script was called incorrectly
 * 1.0.1
 * 	Added support for multiple iframes
 * 1.0.0
 * 	Initial Release
 */
class SpecialOffersSnippet extends FeatureWithAssets{
	const baseurl = 'http://hotelsitecontents.fastbooking.com/promotions.php';
	const default_divdest = 'FB_so';
	
	public $params;
	private $url;
	public $index;
	public $templates;
	//private $assets = array('js', 'css');

	public function __construct($hid){
		$tpl = <<< EOF
	<div id="%divdest%">
		<div class="loading">%loading%</div>
	</div>
	<iframe id="iframe_%divdest%" src="%iframe%" style="display:none"></iframe>
EOF;
		
		$this->templates = new SubstitutionTemplate();
		$this->templates
			->set_tpl($tpl)
			->set_markup('loading', __('Loading Offers...', 'theme'));
		function my_scripts_method(){
			wp_register_script(
				'snippet-com', 
				'http://hotelsitecontents.fastbooking.com/js/com.js',
				null,
				'0.1',
				true
			);
		}
		add_action('wp_enqueue_scripts', 'my_scripts_method');
		$this
			->add_asset('snippet-com', 'js')
			->add_param('hid', $hid)
			->add_param('divdest', self::default_divdest);
		$this->index = 0;
		if(!is_admin() && $this->load_assets());
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
	 * Adds a new parameter or update if exists
	 * @param string $key
	 * @param int|string $value
	 * @return SpecialOffersSnippet $this for chainability
	 */
	public function add_param($key, $value){
		$this->params[$key] = $value;
		return $this;
	}
	
	/**
	 * Retrives the $key param for this snippet
	 * @param string $key name of the parameter
	 */
	public function get_param($key){
		return $this->params[$key];
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
		$url_arr = parse_url(self::baseurl);
		$url_arr['query'] = http_build_query($this->params);
		return self::build_url($url_arr);
	}
	
	/**
	 * Calculates the divdest for the current iframe integration
	 * First time it will be self::default_divdest
	 * For the other an "_<number>" will be appended
	 * If the parameter divdest is customized it will return that one
	 */
	protected function get_divdest(){
		if(substr($this->get_param('divdest'), 0, strlen(self::default_divdest)) ==  self::default_divdest){
			if($this->index == 0){
				return self::default_divdest;
			} else {
				return self::default_divdest.'_'.$this->index;
			}
		}
		return $this->get_param('divdest');
	}
	
	/**
	 * Retrieves the markup for the offers
	 */
	public function get_markup(){
		$this->add_param('divdest', $this->get_divdest());
		$this->index++;
		
		return $this->templates
			->set_markup('iframe', $this->get_iframe_src())
			->set_markup('divdest', $this->get_param('divdest'))
			->replace_markup();
	}
	
	/**
	 * Prints the iframe src
	 */
	public function the_iframe_src(){
		print($this->get_iframe_src());
		return $this;
	}


}



