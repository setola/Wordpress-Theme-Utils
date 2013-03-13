<?php 
/**
 * Contains the DefaultAssets class
 */

/**
 * Register some useful assets in WordPress
 * @author etessore
 * @version 1.0.1
 */
class DefaultAssets{
	/**
	 * @var arrary manages the list of css and js 
	 */
	private $assets = array('css' => array(), 'js' => array());
	
	/**
	 * @var array stores infos on path and uri
	 */
	private $base_dir = array();
	
	/**
	 * Register some assets and hooks into WP
	 */
	function __construct(){
		$this
			->register_standard()
			->register_custom()
			->hook();
	}
	
	/**
	 * Register some assets for generic use
	 * Put here yout JS libraries and Generic CSS like reset.css or similar
	 * @return DefaultAssets $this for chainability
	 */
	public function register_standard(){
		
		/**
		 * Jquery UI replacement for the conflicting simlinked js in the wp installation
		 */
		$this->add_js('jquery', '/js/jquery.min.js', null, '1.7.2', true);
		$this->add_js('jquery-ui-core', '/js/jquery.ui.core.min.js', array('jquery'), '1.8.20', true);
		$this->add_js('jquery.ui.selectmenu', '/js/jquery.ui.selectmenu.js', array('jquery','jquery-ui-core','jquery-ui-position','jquery-ui-widget'), '1.2.1', true);
		$this->add_js('jquery-ui-position', '/js/jquery.ui.position.min.js', array('jquery','jquery-ui-core'), '1.8.20', true);
		$this->add_js('jquery-ui-widget', '/js/jquery.ui.widget.min.js', array('jquery','jquery-ui-core'), '1.8.20', true);
		
		
		/**
		 * Some usefull jquery libraries
		 */
		$this->add_js('jquery.imagesloaded', '/js/jquery.imagesloaded.js', array('jquery'), '2.0.1', true);
		$this->add_js('jquery.cycle', '/js/jquery.cycle.js', array('jquery'), '2.0.1', true);
		$this->add_js('jquery.scrollto', '/js/jquery.scrollTo.js', array('jquery'), '1.4.2', true);
		$this->add_js('jquery-fancybox', '/js/jquery.fancybox.js', array('jquery'), '2.1.0', true);
		
		
		
		
		/**
		 * Fblib, remember to fill the fbcallback.js in your child theme!
		 */
		$this->add_js('fbqs', 'http://static.fbwebprogram.com/fbcdn/fastqs/fastbooking_loader.php?v=1&callbackScriptURL='
				.get_stylesheet_directory_uri().'/js/fbcallback.js', array('jquery','jquery.ui.selectmenu'), '1', true);
		
		
		/**
		 * HTML5 Boiler Plate
		 */
		$this->add_css('h5bp.normalize', '/html5-boilerplate/css/normalize.css', null, '1.0.1', 'screen');
		$this->add_css('h5bp.main', '/html5-boilerplate/css/main.css', null, '1.0.1', 'screen');
		
		
		
		
		return $this;
	}
	
	/**
	 * Register some assets to be used in the theme.
	 * Put here your custom scripts.
	 * @return DefaultAssets $this for chainability
	 */
	public function register_custom(){
		
		/**
		 * Initialization scripts
		 */
		$this->add_js('slideshow', '/js/slideshow.js', array('jquery.imagesloaded', 'jquery.cycle'), '0.1', true);
		$this->add_js('slideshow-fullscreen', '/js/slideshow-fullscreen.js', array('jquery.imagesloaded', 'jquery.cycle', 'jquery.scrollto'), '0.1', true);
		$this->add_js('social', '/js/social.js', array('jquery'), '0.1', true);
		$this->add_js('cycle', '/js/cycle.js', array('jquery.imagesloaded', 'jquery.cycle'), '0.1', true);
		$this->add_js('crs', '/js/crs.js', array('jquery'), '0.1', true);
		$this->add_js('open-details', '/js/open-details.js', array('jquery'), '0.1', true);
		$this->add_js('navbar-fixed', '/js/navbar-fixed.js', array('jquery'), '0.1', true);
		$this->add_js('photogallery', '/js/photogallery.js', array('jquery','jquery-fancybox'), '0.1', true);
		$this->add_js('tabs', '/js/tabs.js', array('jquery'), '0.1', true);
		$this->add_js('modernizr', '/html5-boilerplate/js/vendor/modernizr-2.6.1.min.js', null, '2.6.1', false);
		$this->add_js('minigallery-thumbs-link-to-big', '/js/minigallery-thumbs-link-to-big.js', array('jquery-fancybox'), '0.1', false);
		
		
		/**
		 * Google Map
		 */
		$this->add_js('gmaps.api', 'http://maps.google.com/maps/api/js?sensor=false', null, '3', 'screen');
		$this->add_js('map', '/js/gmap.js', array('jquery','gmaps.api'), '0.1', 'screen');
		$this->add_js('map-directions', '/js/map-directions.js', array('jquery','map'), '0.1', true);
		
		
		/**
		 * Theme Specific CSS for part of page
		 */
		$this->add_css('reset', '/css/reset.css', null, '2.0', 'screen');
		$this->add_css('sprite', '/css/sprite.css', null, '0.1', 'screen');
		$this->add_css('grid-960', '/css/grid-960.css', null, '0.1', 'screen');
		$this->add_css('fbqs', '/css/fbqs.css', null, '0.1', 'screen');
		$this->add_css('standard-style', '/css/standard-style.css', null, '0.1', 'screen');
		$this->add_css('slideshow-fullscreen', '/css/slideshow-fullscreen.css', null, '0.1', 'screen');
		$this->add_css('room', '/css/room.css', null, '0.1', 'screen');
		$this->add_css('offers', '/css/offers.css', null, '0.1', 'screen');
		$this->add_css('location', '/css/location.css', null, '0.1', 'screen');
		$this->add_css('photogallery', '/css/photogallery.css', null, '0.1', 'screen');
		$this->add_css('open-details', '/css/open-details.css', null, '0.1', 'screen');
		$this->add_css('jquery-fancybox', '/css/jquery.fancybox.css', null, '2.1.0', 'screen');
		$this->add_css('linear-menu', '/css/linear-menu.css', null, '0.1', 'screen');
		
		return $this;
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
	 */
	public function hook(){
		if(!is_admin())
			add_action('init', array($this, 'callback'));
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