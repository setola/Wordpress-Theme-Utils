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
 * The first time a page is called stores the list
 * of needed assets in a transient and then it
 * loads the required assets before the page render.
 *
 * WARNING: This is a king of chache so it can cause
 * some issues on the first page load, if some assets
 * (css first) have to be loaded in the head part.
 * From the second one the page will have
 * all the needed js and css load from the db.
 *
 * @author etessore
 * @version 1.0.0
 * @package classes
 *
 */

abstract class AutomaticAssetsManager {

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
	 * Register some assets and hooks into WP
	 */
	function __construct(){
		$this
			->register_standard()
			->register_custom()
			->hook()
			->enable_automatic_manager();
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
		add_action('wp_enqueue_scripts', array(&$this, 'load_assets'), 9);
		add_action('shutdown', array(&$this, 'save_assets'));
		return $this;
	}
	
	/**
	 * Disables the loading of assets from template part
	 * @return DefaultAssets $this for chainability
	 */
	public function disable_automatic_manager(){
		remove_action('wp_enqueue_scripts', array(&$this, 'load_assets'), 9);
		remove_action('shutdown', array(&$this, 'save_assets'));
		return $this;
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

	/**
	 * Saves the assets required for post with given id
	 *
	 * Stores it in a transient so that if you like to call ThemeHelpers::load_js()
	 * somewhere after wp_head() the next time you will load the page
	 * the system will add such assets to the head (css) or foot (js).
	 * @param int $post_id the id of the post
	 */
	public function save_assets($post_id = null){
		$post_id = (empty($post_id)) ? get_the_ID() : $post_id;
		$transient = 'page_assets_id_'.$post_id;
		delete_transient($transient);
		set_transient($transient, ThemeHelpers::$assets);
	}

	/**
	 * Loads the assets for the post with given id
	 *
	 * Retrieves a transient with the list of assets used in this page\post.
	 * Then enqueue them with {@link http://codex.wordpress.org/Function_Reference/wp_enqueue_script wp_enqueue_script()}
	 * or {@link http://codex.wordpress.org/Function_Reference/wp_enqueue_style wp_enqueue_style()}
	 * @param string $post_id
	 */
	public function load_assets($post_id = null){
		$post_id = (empty($post_id)) ? get_the_ID() : $post_id;
		if(empty($post_id)) $post_id = get_option('page_on_front');
		$transient = 'page_assets_id_'.$post_id;
		$assets = get_transient($transient);
		foreach((array)$assets['css'] as $handle) wp_enqueue_style($handle);
		foreach((array)$assets['js'] as $handle) wp_enqueue_script($handle);
	}

	/**
	 * Register some assets for generic use
	 * Put here yout JS libraries and Generic CSS like reset.css or similar
	 * @return DefaultAssets $this for chainability
	 */
	abstract public function register_standard();

	/**
	 * Register some assets to be used in the theme.
	 * Put here your custom scripts.
	 * @return DefaultAssets $this for chainability
	*/
	abstract public function register_custom();

}