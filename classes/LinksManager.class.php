<?php 
/*
 * stores the class for LinksManager feature
 */

/**
 * Manages the links subsection
 * 
 * Adds support for link image and translations
 * @author etessore
 * @version 1.0.0
 *
 */
class LinksManager{
	/**
	 * Stores the instance of the singleton
	 * @var LinksManager
	 */
	private static $instance = null;
	
	const CUSTOM_LINKS_POST_TYPE = 'wtu-custom-links';
	
	/**
	 * Initializes default settings
	 * Singleton private constructor
	 */
	private function __construct(){
		add_action('init', array(__CLASS__, 'init'));
		add_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		add_action('save_post', array(__CLASS__, 'save_link_data'));
		add_filter('gettext', array(__CLASS__, 'custom_title'));
	}
	
	/**
	 * Called by WordPress on init
	 */
	public function init(){
		self::register_custom_types();
		add_post_type_support(self::CUSTOM_LINKS_POST_TYPE, 'thumbnail');
		if(is_admin()){
			
		}else{
			
		}
	}
	
	/**
	 * Retrieves the current instance
	 * @return LinksManager the current instance
	 */
	public static function get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Test if this feature is enabled
	 * @return boolean true if LinkManager is enabled
	 */
	public static function is_enabled(){
		return !is_null(self::$instance);
	}
	
	/**
	 * Callback to modify the title field in the admin panel
	 * @param unknown $input
	 * @return string|unknown
	 */
	public function custom_title($input) {
	
		global $post_type;
	
		if( is_admin() && 'Enter title here' == $input && self::CUSTOM_LINKS_POST_TYPE == $post_type )
			return __('Enter URL here', 'wtu_framework');
	
		return $input;
	}
	
	/**
	 * Register some custom post types and categories
	 */
	public function register_custom_types(){
		register_post_type(
			self::CUSTOM_LINKS_POST_TYPE, 
			array(
				'label' 				=> 'Links',
				'description' 			=> __('A translable link with image', 'wtu_framework'),
				'public' 				=> true,
				'show_ui' 				=> true,
				'show_in_menu' 			=> true,
				'capability_type' 		=> 'post',
				'hierarchical' 			=> false,
				'rewrite' 				=> array('slug' => ''),
				'query_var' 			=> true,
				'exclude_from_search' 	=> true,
				'supports' 				=> array('title', 'thumbnail','author'),
				'taxonomies' 			=> array('category',),
				'labels' 				=> array (
					'name' 					=> __('Links', 'wtu_framework'),
					'singular_name' 		=> __('Link', 'wtu_framework'),
					'menu_name'				=> __('Links', 'wtu_framework'),
					'add_new'				=> __('Add Link', 'wtu_framework'),
					'add_new_item'			=> __('Add New Link', 'wtu_framework'),
					'edit'					=> __('Edit', 'wtu_framework'),
					'edit_item'				=> __('Edit Link', 'wtu_framework'),
					'new_item'				=> __('New Link', 'wtu_framework'),
					'view'					=> __('View Link', 'wtu_framework'),
					'view_item'				=> __('View Link', 'wtu_framework'),
					'search_items'			=> __('Search Links', 'wtu_framework'),
					'not_found'				=> __('No Links Found', 'wtu_framework'),
					'not_found_in_trash'	=> __('No Links Found in Trash', 'wtu_framework'),
					'parent'				=> __('Parent Link', 'wtu_framework'),
				),
			) 
		);
	}
	
	/**
	 * Print the markup for the link details box
	 * @param object $post the current post
	 */
	public static function edit_link_box_html($post){
		wp_nonce_field(__FILE__, self::CUSTOM_LINKS_POST_TYPE.'_nonce');
		
		// The actual fields for data entry
		// Use get_post_meta to retrieve an existing value from the database and use the value for the form
		$value = get_post_meta($post->ID, '_link_meta_values', true);
		
		$subs = new SubstitutionTemplate();
		$subs->set_tpl('<tr><th scope="row">%th%</th><td>%td%</td></tr>');
		
		echo '<table class="form-table"><tbody>';
		
		echo $subs
			->set_markup('th', HtmlHelper::label(__('Label', 'wtu_framework'), 'link_meta_values[label]'))
			->set_markup('td', HtmlHelper::input('link_meta_values[label]', 'text', array('value'=>$value['label'],'class'=>'large-text')))
			->replace_markup();
		
		echo $subs
			->set_markup('th', HtmlHelper::label(__('Title', 'wtu_framework'), 'link_meta_values[title]'))
			->set_markup('td', HtmlHelper::input('link_meta_values[title]', 'text', array('value'=>$value['title'],'class'=>'large-text')))
			->replace_markup();
		
		echo $subs
			->set_markup('th', HtmlHelper::label(__('Open in new tab', 'wtu_framework'), 'link_meta_values[open_new_tab]'))
			->set_markup('td', HtmlHelper::input('link_meta_values[open_new_tab]', 'checkbox', array('checked' => ($value['open_new_tab']=='on') ? 'checked' : '')))
			->replace_markup();
		
		echo $subs
			->set_markup('th', HtmlHelper::label(__('Noindex', 'wtu_framework'), 'link_meta_values[noindex]'))
			->set_markup('td', HtmlHelper::input('link_meta_values[noindex]', 'checkbox', array('checked' => ($value['noindex']=='on') ? 'checked' : '')))
			->replace_markup();
		
		echo '</tbody></table>';
		
	}
	
	/**
	 * Register the link details metabox and removes some useless one
	 */
	public static function register_metaboxes(){
		remove_meta_box('fbseo', self::CUSTOM_LINKS_POST_TYPE, 'normal');
		remove_meta_box('wpseo_meta', self::CUSTOM_LINKS_POST_TYPE, 'normal');
		add_meta_box(
			'wpu-custom-links',
			__( 'Link Details', 'wtu_framework' ),
			array(__CLASS__, 'edit_link_box_html'),
			self::CUSTOM_LINKS_POST_TYPE,
			'normal',
			'high'
		);
	}
	
	/**
	 * Called by WordPress while saving the post
	 * @param int $post_id the post ID
	 */
	public static function save_link_data($post_id){
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
			!isset($_POST[self::CUSTOM_LINKS_POST_TYPE.'_nonce']) 
			|| !wp_verify_nonce($_POST[self::CUSTOM_LINKS_POST_TYPE.'_nonce'], __FILE__) 
		) {
			return;
		}
		
		
		// add some input sanification
		$post_id = intval($_POST['post_ID']);
		
		$sanitized_data = array();
		foreach((array)$_POST['link_meta_values'] as $k => $v){
			$sanitized_data[$k] = sanitize_text_field($v);
		}
		
		update_post_meta($post_id, '_link_meta_values', $sanitized_data);
	}
	
	
	public static function get_links($args=array()){
		$defaults = array(
			'post_type'	=>	self::CUSTOM_LINKS_POST_TYPE
		);
		$args = wp_parse_args( $args, $defaults );
		return get_posts($args);
	}
}