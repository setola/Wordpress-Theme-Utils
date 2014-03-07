<?php 



/**
 * Stores some functions and features needed for 
 * finding blog ids based on the theme they are using
 * @author Emanuele 'Tex' Tessore
 * @version 1.0.0
 */
class ThemeGroup{
	/**
	 * Stores the name of the option where the cache is stored
	 * @var string
	 */
	const TRANSIENT_NAME 	= '_transient_theme_group_cache';

	/**
	 * Stores the list of all available blogs
	 * @var array
	 */
	private $available_blogs = null;

	/**
	 * Stores an array of themes where the key is the theme
	 * and the value and array of blogs id that use it
	 * @var array
	 */
	private $themes = null;

	/**
	 * Stores the singleton instance
	 * @var BluHotels
	 */
	public static $instance = null;


	private function __construct(){
		$this->hook();
	}
	
	public function hook(){
		add_action('init', array(&$this, 'on_init'));
		add_action('shutdown', array(&$this, 'on_shutdown'));
	}

	/**
	 * Called by WordPress on init action
	 * Retrieve the cache from transient.
	 */
	public function on_init(){
		$meta = get_transient(self::TRANSIENT_NAME);
		
		if($meta['available_blogs']) {
			$this->available_blogs = $meta['available_blogs'];
		}

		if($meta['themes']){
			$this->themes = $meta['themes'];
		}
	}

	/**
	 * Called by WordPress on shutdown action
	 * Set the cache up if needed
	 */
	public function on_shutdown(){
		$meta = get_transient(self::TRANSIENT_NAME);

		if(!$meta){
			$meta = array(
					'available_blogs'	=>	$this->available_blogs,
					'themes'			=>	$this->themes
			);
			set_transient(self::TRANSIENT_NAME, $meta);
		}
	}
	
	/**
	 * Flushes the cache
	 */
	public function flush(){
		delete_transient(self::TRANSIENT_NAME);
	}

	/**
	 * Retrieves the singleton instance of the metabox
	 * @return BluHotels unique instance
	 */
	public static function get_instance(){
		if(self::$instance != null) return self::$instance;
		
		$c = get_called_class();
		self::$instance = new $c;
		return self::$instance;
	}

	/**
	 * Retrieves the list of all non deleted blogs
	 * @return array the list of blog ids
	 */
	public function get_all_blogs(){
		global $wpdb;
		if(is_null($this->available_blogs)){
			$this->available_blogs = $wpdb->get_col("SELECT blog_id FROM " . $wpdb->blogs . " WHERE deleted = 0");
		}
		return $this->available_blogs;
	}

	/**
	 * Retrieves all the blogs id tha are currently using the given theme
	 * @param string $theme the theme to be checked, if null the complete themes list is returned
	 * @return array list of blogs using the given theme
	 */
	public function get_blogs_with_theme($theme=null){
		global $wpdb;

		if(is_null($this->themes)){
			$this->themes = array();
			foreach($this->get_all_blogs() as $blog_id){
				$t = $wpdb->get_var(
						"SELECT option_value FROM " . $wpdb->get_blog_prefix($blog_id). 'options'
						. " WHERE option_name = 'stylesheet'"
				);
				$this->themes[$t][] = $blog_id;
			}
		}

		if(is_null($theme)) {
			return $this->themes;
		}

		return (array)$this->themes[$theme];
	}
}









