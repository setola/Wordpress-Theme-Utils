<?php 
/**
 * containst SpecialOffersSnippet class definition
 */

/**
 * Manages the integration of the special offers snippet
 * @author etessore
 * @version 1.0.4
 * @package classes
 */
 
/* 
 * Changelog:
 * 1.0.4
 * 	new javascript implementation: FB.CrossCom.consume();
 * 1.0.3
 * 	remove dependency from FeatureWithAsset and used ThemeHelpers::load_js()
 * 1.0.2
 * 	Fixed Notice: wp_enqueue_script was called incorrectly
 * 1.0.1
 * 	Added support for multiple iframes
 * 1.0.0
 * 	Initial Release
 */
class SpecialOffersSnippet {
	const baseurl = 'http://hotelsitecontents.fastbooking.com/router.php?snippet=promotion';
	const default_divdest = 'FB_so';
	const com_js = 'http://hotelsitecontents.fastbooking.ch/js/fb.js';
	const pop_js = 'http://hotelsitecontents.fastbooking.com/js/pop.js';
	
	/**
	 * @var array stores the list of parameter to be passed on GET
	 */
	public $params;
	
	/**
	 * @var string stores the url of the snippet
	 */
	private $url;
	
	/**
	 * @var int the index of snippet occurency
	 */
	public $index;
	
	/**
	 * @var SubstitutionTemplate the template to be substituted
	 */
	public $templates;
	//private $assets = array('js', 'css');

	/**
	 * Initializes the snippet
	 * @param string $hid the hotel hid
	 */
	public function __construct($hid){
		$tpl = <<< EOF
	%comjstag%
	%popjstag%
	<div id="%divdest%"%option_divdest%>
		<div class="loading">%loading%</div>
	</div>
	%promojstag%
EOF;
		
		$this->templates = new SubstitutionTemplate();
		$this->templates
			->set_tpl($tpl)
			->set_markup('option_divdest', '')
			->set_markup('loading', __('Loading Offers...', 'theme'));
		
		$this->add_param('hid', $hid)
			->add_param('divdest', self::default_divdest);
		
		$this->index = 0;
		
		if(!is_admin()){
			ThemeHelpers::load_js('snippet-com');
		}
	}

	/**
	 * Builds the url for the remote iframe
	 * @param array $params additional query params for the iframe
	 */
	public static function calculate_url($params=array()){
		$url_arr = parse_url(self::baseurl);
		$url_arr['query'] = 
			(empty($url_arr['query'])) 
				?	http_build_query($params) 
				:	$url_arr['query'].'&'.http_build_query($params);
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
	 * Enables the offers to cycle
	 * @return SpecialOffersSnippet $this for chainability
	 */
	public function enable_cycle(){
		$this->templates->set_markup('option_divdest', 'class="cycle"');
		ThemeHelpers::load_js('offers-cycle');
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
		return self::calculate_url($this->params);
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
	 * Retrieves the script tag for com.js
	 * 
	 * Checks if is the first implementation, otherwhise it will return an empty string
	 * @return string html script tag to com.js
	 */
	protected function get_com_js(){
		if($this->index == 1)
			return 
				HtmlHelper::script(
					'/* */', 
					array(
						'src'	=>	self::com_js,
						'id'	=>	'script_com_js'
					)
				);
		return '';
	}
	
	/**
	 * Retrieves the script for initialize the snippet system
	 * @return string html script tag
	 */
	protected function get_promo_js(){
		$content = new SubstitutionTemplate();
		$content
			->set_tpl('FB.CrossCom.consume("%url%", "%divedest%");')
			->set_markup('url', $this->get_iframe_src())
			->set_markup('divedest', $this->get_param('divdest'));
		return 
			HtmlHelper::script(
				$content->replace_markup(), 
				array(
					'id'=>'iframe_'.$this->get_param('divdest')
				)
			);
	}
	
	/**
	 * Gets the pop.js for the 'ugly pop-up' feature
	 * @return string
	 */
	protected function get_pop_js(){
		if($this->index == 1)
			return 
				HtmlHelper::script(
					'/* */', 
					array(
						'src'	=>	self::pop_js,
						'id'	=>	'script_com_js'
					)
				);
			return '';
	}
	
	/**
	 * Retrieves the markup for the offers
	 */
	public function get_markup(){
		$this->add_param('divdest', $this->get_divdest());
		$this->index++;
		
		return $this->templates
			//->set_markup('iframe', $this->get_iframe_src())
			->set_markup('divdest', $this->get_param('divdest'))
			->set_markup('comjstag', $this->get_com_js())
			->set_markup('popjstag', $this->get_pop_js())
			->set_markup('promojstag', $this->get_promo_js())
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



