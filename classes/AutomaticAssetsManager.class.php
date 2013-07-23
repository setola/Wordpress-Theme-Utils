<?php 
/**
 * Contains AutomaticAssetsManager class
 */


/**
 * Manages the assets
 *
 * Adds the possibility to load a javascript or a css
 * in the middle in a template part without
 * having the need to check if the asset can be
 * loaded in the head or in the foot of the page.
 *
 * Uses ob_start and a placeholder for the assets.
 * It's a singleton beacause it has to be run only once
 *
 * @author etessore
 * @version 1.0.0
 * @package classes
 *
 */

class AutomaticAssetsManager {
	
	private static $instance = null;

	/**
	 * manages the list of css and js
	 * @var arrary
	 */
	private $assets = array('css' => array(), 'js' => array());

	/**
	 * stores infos on path and uri
	 * @var array
	 */
	private $base_dir = array();
	
	/**
	 * Stores the status for this feature
	 * @var boolean true if the feature is enabled
	 */
	private $status = false;
	
	/**
	 * Placeholder for the css
	 * @var string
	 */
	const CSS_MARKER = '<!-- ###WPU### place here the css -->';
	
	/**
	 * Placeholder for the top javascripts
	 * @var stirng
	 */
	const TOP_JS_MARKER = '<!-- ###WPU### place here the top js -->';
	
	/**
	 * Placeholder for the bottom javascript
	 * @var string
	 */
	const BOTTOM_JS_MARKER = '<!-- ###WPU### place here the bottom js -->';

	/**
	 * Register some assets and hooks into WP
	 */
	private function __construct(){
		$this
			->hook()
			->enable_automatic_manager();
	}
	
	/**
	 * Retrieves the singleton instance for this feature
	 * @return AutomaticAssetsManager unique instance
	 */
	public static function get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Adds a javascript to the current set
	 * @see @link http://codex.wordpress.org/Function_Reference/wp_register_script
	 * @param string $handle Script name
	 * @param string $src Script url
	 * @param array $deps (optional) Array of script names on which this script depends
	 * @param string|bool $ver (optional) Script version (used for cache busting), set to NULL to disable
	 * @param bool $in_footer (optional) Whether to enqueue the script before </head> or before </body>
	 * @return DefaultAssets $this for chainability
	 */
	public function add_js($handle, $src, $deps = array(), $ver = null, $in_footer = false){
		$this->assets['js'][$handle] = array(
				'handle'	=>	$handle,
				'src'		=>	$src,
				'deps'		=>	$deps,
				'ver'		=>	$ver,
				'in_footer'	=>	$in_footer
		);
		return $this;
	}

	/**
	 * Adds a css to the current set
	 * @see @link http://codex.wordpress.org/Function_Reference/wp_register_style
	 * @param string $handle Name of the stylesheet.
	 * @param string|bool $src Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
	 * @param array $deps Array of handles of any stylesheet that this stylesheet depends on.
	 *  (Stylesheets that must be loaded before this stylesheet.) Pass an empty array if there are no dependencies.
	 * @param string|bool $ver String specifying the stylesheet version number. Set to NULL to disable.
	 *  Used to ensure that the correct version is sent to the client regardless of caching.
	 * @param string $media The media for which this stylesheet has been defined.
	 * @return DefaultAssets $this for chainability
	 */
	public function add_css($handle, $src, $deps = array(), $ver = null, $media = false){
		$this->assets['css'][$handle] = array(
				'handle'	=>	$handle,
				'src'		=>	$src,
				'deps'		=>	$deps,
				'ver'		=>	$ver,
				'media'		=>	$media
		);
		return $this;
	}


	/**
	 * Hooks the assets registration into wordpress init hook
	 * @return DefaultAssets $this for chainability
	 */
	public function hook(){
		if(!is_admin()){
			add_action('wp_enqueue_scripts', array(&$this, 'callback'), 8);
		}
		return $this;
	}
	
	/**
	 * Enables the loading of assets from template part
	 * @return DefaultAssets $this for chainability
	 */
	public function enable_automatic_manager(){
		if(!$this->status && !is_admin()){
			remove_action('wp_head', 'wp_print_styles', 8);
			
			add_action('wp_head', array(&$this, 'on_wp_print_styles'));
			add_action('wp_head', array(&$this, 'on_wp_print_scripts'));
			add_action('wp_footer', array(&$this, 'on_wp_print_footer_scripts'));
			
			add_action('shutdown', array(&$this, 'obstart_replace_assets_marker'), -11);
			add_action('init', array(&$this, 'obstart_init'));
		}
		$this->status = true;
		return $this;
	}
	
	/**
	 * Called by wordpress right before the page start 
	 */
	public function obstart_init(){
		ob_start();
	}
	
	/**
	 * Disables the loading of assets from template part
	 * @return DefaultAssets $this for chainability
	 */
	public function disable_automatic_manager(){
			
		remove_action('wp_head', array(&$this, 'on_wp_print_styles'));
		remove_action('wp_head', array(&$this, 'on_wp_print_scripts'));
		remove_action('wp_footer', array(&$this, 'on_wp_print_footer_scripts'));
		
		remove_action('shutdown', array(&$this, 'obstart_replace_assets_marker'), -11);
		remove_action('init', array(&$this, 'obstart_init'));
		
		$this->status = false;
		return $this;
	}

	/**
	 * Prints a marker for the top css
	 */
	public function on_wp_print_styles(){ echo "\n".self::CSS_MARKER."\n"; }
	
	/**
	 * Prints a marker for the top javascript
	 */
	public function on_wp_print_scripts(){ echo "\n".self::TOP_JS_MARKER."\n"; }
	
	/**
	 * Prints a marker for the bottom javascript
	 */
	public function on_wp_print_footer_scripts(){ echo "\n".self::BOTTOM_JS_MARKER."\n"; }
	
	/**
	 * Replaces the temporarily markers inserted
	 * on first page execution with the real
	 * list of html markup for the needed assets
	 */
	public function obstart_replace_assets_marker(){
		$this->disable_automatic_manager();
		$html = ob_get_clean();
		
		foreach((array)ThemeHelpers::$assets['css'] as $handle) wp_enqueue_style($handle);
		foreach((array)ThemeHelpers::$assets['js'] as $handle) wp_enqueue_script($handle);
		
		$css_render = '';
		$top_js_render = '';
		$bottom_js_render = '';
		
		ob_start();
		wp_print_styles();
		$css_render = ob_get_clean();
		
		ob_start();
		do_action('wp_print_scripts');
		$top_js_render = ob_get_clean();
		
		ob_start();
		do_action('wp_print_footer_scripts');
		$bottom_js_render = ob_get_clean();/**/
		
		echo str_replace(
			array(self::CSS_MARKER, self::TOP_JS_MARKER, self::BOTTOM_JS_MARKER), 
			array($css_render, $top_js_render, $bottom_js_render), 
			$html
		);
	}

	/**
	 * This is called on wordpress init
	 * Tries to load asset from the child theme,
	 * if the asset file doesn't exists it loads
	 * from the parent theme
	 */
	public function callback(){
		foreach($this->assets['js'] as $asset){
			wp_deregister_script($asset['handle']);
			if(file_exists(get_stylesheet_directory().$asset['src'])){
				wp_register_script(
				$asset['handle'],
				get_stylesheet_directory_uri().$asset['src'],
				$asset['deps'],
				$asset['ver'],
				$asset['in_footer']
				);
			} elseif(file_exists(get_template_directory().$asset['src'])) {
				wp_register_script(
				$asset['handle'],
				get_template_directory_uri().$asset['src'],
				$asset['deps'],
				$asset['ver'],
				$asset['in_footer']
				);
			} else {
				wp_register_script(
				$asset['handle'],
				$asset['src'],
				$asset['deps'],
				$asset['ver'],
				$asset['in_footer']
				);
			}
		}
		foreach($this->assets['css'] as $asset){
			wp_deregister_style($asset['handle']);
			if(file_exists(get_stylesheet_directory().$asset['src'])){
				wp_register_style(
				$asset['handle'],
				get_stylesheet_directory_uri().$asset['src'],
				$asset['deps'],
				$asset['ver'],
				$asset['media']
				);
			} elseif(file_exists(get_template_directory().$asset['src'])) {
				wp_register_style(
				$asset['handle'],
				get_template_directory_uri().$asset['src'],
				$asset['deps'],
				$asset['ver'],
				$asset['media']
				);
			} else {
				wp_register_style(
				$asset['handle'],
				$asset['src'],
				$asset['deps'],
				$asset['ver'],
				$asset['media']
				);
			}
		}
	}

}