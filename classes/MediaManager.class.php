<?php 
/**
 * Manages multiple media set
 * 
 * This feature adds a metabox to your WordPress admin panel
 * in which you can add any media media into a given set.
 * 
 * Then in your theme you will be able to retrieve the single
 * set and show it where you need it.
 * 
 * For example if you need to have a slideshow on the top of the page
 * and a minigallery on the bottom, this feature is what you need
 * to separate your images between the two sets.
 * 
 * @author Emanuele 'Tex' Tessore
 * @version 1.0.0
 *
 */
class MediaManager {
	/**
	 * Maintains the status of this feature
	 * @var unknown
	 */
	public static $enabled = false;
	
	/**
	 * Stores the list of needed media types
	 * @var array
	 */
	private static $media_list = array();
	
	/**
	 * Postmeta name to identify a single hotel
	 * @var string
	 */
	const META_KEY_NAME = '_custom_media_manager';
	
	/**
	 * Enable the feature
	 */
	public static function enable() {
		add_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		add_action('save_post', array(__CLASS__, 'save_metabox_data'));
		add_action('init', array(__CLASS__, 'register_assets'));
		add_filter('media_view_settings', array(__CLASS__, 'media_view_settings'), 10, 2);
		//add_action('wp_ajax_wpu-media-manager-update', array(__CLASS__,'wp_ajax_media_manager_gallery_update'));
		add_action('admin_print_scripts', array(__CLASS__, 'enqueue_assets'));
		self::$enabled = true;
	}
	
	/**
	 * Disables the feature
	 */
	public static function disable(){
		remove_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		remove_action('save_post', array(__CLASS__, 'save_metabox_data'));
		remove_action('init', array(__CLASS__, 'register_assets'));
		remove_filter('media_view_settings', array(__CLASS__, 'media_view_settings'), 10, 2);
		remove_action('admin_print_scripts', array(__CLASS__, 'register_assets'));
		self::$enabled = false;
	}
	
	/**
	 * Register scripts and styles needed for this feature
	 */
	public static function register_assets(){
		wp_register_script('wpu-media-manager', get_template_directory_uri().'/js/media-manager.js', array('jquery'), '1.0.0', true);
		wp_register_script('json2', 'http://cdnjs.cloudflare.com/ajax/libs/json2/20121008/json2.js', null, '20121008', true);
	}
	
	/**
	 * Enqueue the needed scripts and styles
	 */
	public static function enqueue_assets(){
		wp_enqueue_script('wpu-media-manager');
		wp_enqueue_script('json2');
		wp_enqueue_media();
		wp_enqueue_script('custom-header');
	}
	
	public static function media_view_settings($settings, $post ) {
		if (!is_object($post)) return $settings;
	
		// Create our own shortcode string here
		$shortcode = 'wpuCustomGallery';
	
		$settings['wpuCustomGallery'] = array('shortcode' => $shortcode);
		return $settings;
	}
	
	public static function wp_ajax_media_manager_gallery_update(){
		
	}
	
	/**
	 * Registers the metabox
	 */
	public static function register_metaboxes(){
		add_meta_box(
			'wpu-media-manager',
			__( 'Media Manager', 'wtu_framework' ),
			array(__CLASS__, 'metabox_html'),
			'page',
			'side',
			'high'
		);
	}
	
	/**
	 * Prints the HTML markup for the metabox
	 */
	public static function metabox_html($post){
		wp_nonce_field(__FILE__, self::META_KEY_NAME.'_nonce');
		$value = get_post_meta($post->ID, self::META_KEY_NAME, true);
		
		if(count(self::$media_list)){
			foreach(self::$media_list as $elem){
				
				echo HtmlHelper::anchor(
					'javascript:;', 
					HtmlHelper::span('', array('class'=>'wp-media-buttons-icon'))
					.sprintf(__('Manage Media for %s', 'wtu_framework'), HtmlHelper::strong($elem['label'])),
					array(
						'class'				=>	'button media-manager-button',
						'data-target'		=>	'#'.$elem['id'],
						'data-title'		=>	sprintf(__('Manage Media for %s', 'wtu_framework'), $elem['label']),
						'data-button-label'	=>	sprintf(__('Add selected media to %s set', 'wtu_framework'), $elem['label']),
						'data-multiple'		=>	'true',
						'title'				=>	sprintf(__('Manage Media for %s', 'wtu_framework'), $elem['label'])
					)
				);
				
				echo HtmlHelper::input(
					self::META_KEY_NAME.'['.$elem['id'].']', 
					'text', 
					array('id'=>$elem['id'],'value'=>$value[$elem['id']])
				);
			
				$number = 0;
				if(isset($value[$elem['id']])) $number = count(explode(',', $value[$elem['id']]));
				if($number>0){
					printf(_n('1 element', '%s elements', $number, 'wtu_framework'), $number); 
				} else {
					_e('Empty', 'wtu_framework');
				}
				
				echo HtmlHelper::br().HtmlHelper::br();
			}
		}
		//var_dump($value);
	}
	
	/**
	 * Saves the metabox data while saving the page
	 * @param int $post_id the post id
	 */
	public static function save_metabox_data($post_id){
		if(!isset($post_id)) return;
		if(!isset($_POST['post_type'])) return;
		// First we need to check if the current user is authorised to do this action.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		// Secondly we need to check if the user intended to change this value.
		if ( 
			!isset($_POST[self::META_KEY_NAME.'_nonce']) 
			|| !wp_verify_nonce($_POST[self::META_KEY_NAME.'_nonce'], __FILE__) 
		) {
			return;
		}
		
		$sanitized = array();
		foreach($_POST[self::META_KEY_NAME] as $k => $v){
			if(is_array($v)){
				foreach($v as $k1 => $v1){
					$v[sanitize_text_field($k1)] = sanitize_text_field(trim($v1, '"'));
				}
				$sanitized[sanitize_text_field($k)] = $v;
			} else {
				$sanitized[sanitize_text_field($k)] = sanitize_text_field(trim($v, '"'));
			}
		}
		
		update_post_meta(
			$_POST['post_ID'], 
			self::META_KEY_NAME, 
			$sanitized
		);
	}
	
	/**
	 * Adds or edits a set 
	 * @param string $id identifier for the set
	 * @param array $parms additional parameters such as the label showed in wp admin page
	 */
	public static function set_media_list($id, $parms){
		if(!isset($parms['id'])) $parms['id'] = $id;
		self::$media_list[$id] = $parms;
	}
	
	/**
	 * Retrieves the elements for the given set.
	 * If $set is empty, every available set will be returned
	 * @param string $set filter only a particular set of elements
	 * @param int $post_id the post ID to be queried
	 * @return mixed a list of media elements for the given set
	 */
	public static function get_media($set='', $post_id=null){
		if(is_null($post_id)) $post_id = get_the_ID();
		$data = get_post_meta($post_id, self::META_KEY_NAME, true);
		if($set!='' && isset($data[$set])) return explode(',', $data[$set]);
		return explode(',', $data);
	}
	
}

