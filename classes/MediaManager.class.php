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
 * In your functions.php simply add
 * <code>
 * 	MediaManager::enable();
 * 	MediaManager::set_media_list('slideshow', array(
 * 		'label'		=>	__('Slideshow', 'wtu_framework'),
 * 		'shortcode'	=>	'mySlideshow'
 * 	));
 * </code>
 * to enable the backend feature.
 * 
 * Now you will have to manage a new shortcode; for example:
 * 
 * <code>
 * 	function mySlideshow_gallery_shortcode($atts){
 * 		$data = shortcode_atts(array('ids' => ''), $atts );
 * 		$toret = '';
 * 		$ids = explode(',', $data['ids']);
 * 		foreach($ids as $id){
 * 			$toret .= wp_get_attachment_image($id);
 * 		}
 * 		return $toret;
 * 	}
 * 	
 * 	add_shortcode('mySlideshow', 'mySlideshow_gallery_shortcode');
 * </code>
 * 
 * If you need more than a section simply duplicate your code and change 
 * at least the shortcode and the id (first parameter of set_media_list())
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
	protected static $media_list = array();
	
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
		//add_action('save_post', array(__CLASS__, 'save_metabox_data')); //done by ajax
		add_action('init', array(__CLASS__, 'register_assets'));
		//add_filter('media_view_settings', array(__CLASS__, 'media_view_settings'), 10, 2);
		add_action('wp_ajax_wpu-media-manager-update', array(__CLASS__,'wp_ajax_media_manager_gallery_update'));
		add_action('wp_ajax_wpu-media-manager-delete', array(__CLASS__,'wp_ajax_media_manager_gallery_delete'));
		add_action('admin_print_scripts', array(__CLASS__, 'enqueue_assets'));
		self::$enabled = true;
	}
	
	/**
	 * Disables the feature
	 */
	public static function disable(){
		remove_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		//remove_action('save_post', array(__CLASS__, 'save_metabox_data'));
		remove_action('init', array(__CLASS__, 'register_assets'));
		//remove_filter('media_view_settings', array(__CLASS__, 'media_view_settings'), 10, 2);
		remove_action('wp_ajax_wpu-media-manager-update', array(__CLASS__,'wp_ajax_media_manager_gallery_update'));
		remove_action('admin_print_scripts', array(__CLASS__, 'register_assets'));
		self::$enabled = false;
	}
	
	/**
	 * Register scripts and styles needed for this feature
	 */
	public static function register_assets(){
		wp_register_script('wpu-media-manager', get_template_directory_uri().'/js/media-manager.js', array('jquery','media-editor'), '1.0.0', true);
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
	
	/**
	 * Called on media_view_settings, this method adds a new 
	 * shortcode for the media manager custom gallery
	 * @param mixed $settings media view settings
	 * @param object $post the post
	 * @return mixed settings with new shortcode
	 * @deprecated useless???
	 */
	public static function media_view_settings($settings, $post){
		if (!is_object($post)) return $settings;
		$settings['wpuCustomGallery'] = array('shortcode' => 'wpuCustomGallery');
		return $settings;
	}
	
	/**
	 * Ajax callback for saving the custom gallery ids list
	 */
	public static function wp_ajax_media_manager_gallery_update(){

		header('Content-type: text/json');
		
		$error = array('success'=>false, 'data'=>'');
		
		//TODO: check of the nonce!!!!!!!
		if(isset($_POST['html'])){
			$html = sanitize_text_field($_POST['html']);
		} else {
			$error['data'] = 'html not set';
			die(json_encode($error));
		}
		
		if(isset($_POST['post_id'])){
			$post_id = intval($_POST['post_id']);
		} else {
			$error['data'] = 'post id not set';
			die(json_encode($error));
		}
		
		if(isset($_POST['elem_id'])){
			$meta_name = self::META_KEY_NAME.'-'.sanitize_title($_POST['elem_id']);
		} else {
			$error['data'] = 'element id not set';
			die(json_encode($error));
		}
		
		update_post_meta($post_id, $meta_name, $html);
		
		// I want to be absolutely sure to return the meta currently present in the db :)
		$toret = array('success'=>true, 'data'=>get_post_meta($post_id, $meta_name, true));
		
		die(json_encode($toret));
	}
	
	/**
	 * Ajax callback for deleting a custom media set
	 */
	public function wp_ajax_media_manager_gallery_delete(){
		header('Content-type: text/json');
		
		$toret = array('success'=>false, 'data'=>'');
		if(isset($_POST['post_id'])){
			$post_id = intval($_POST['post_id']);
		} else {
			$toret['data'] = 'post id not set';
			die(json_encode($toret));
		}
		
		if(isset($_POST['elem_id'])){
			$meta_name = self::META_KEY_NAME.'-'.sanitize_title($_POST['elem_id']);
		} else {
			$toret['data'] = 'element id not set';
			die(json_encode($toret));
		}
		
		$data = get_post_meta($post_id, $meta_name, true);
		
		if(delete_post_meta($post_id, $meta_name)){
			$toret['success'] 	= true;
			$toret['data'] 		= $data;
		} else {
			$toret['data']		= 'Error while deleting meta';
		}
		
		die(json_encode($toret));
	}
	
	/**
	 * Registers the metabox
	 * 
	 * @uses add_meta_box
	 * 
	 * @param string $post_type The type of Write screen on 
	 * which to show the edit screen section ('post', 'page', 'dashboard', 
	 * 'link', 'attachment' or 'custom_post_type' where custom_post_type 
	 * is the custom post type slug)
	 * 
	 * @param string $context The part of the page where the edit screen 
	 * section should be shown ('normal', 'advanced', or 'side'). 
	 * (Note that 'side' doesn't exist before 2.7)
	 * 
	 * @param string $priority The priority within the context where the 
	 * boxes should show ('high', 'core', 'default' or 'low')
	 */
	public static function register_metaboxes($post_type='', $context='side', $priority='high'){
		global $post;
		$template = get_post_meta($post->ID, '_wp_page_template', true);
		$show_meta_box = false;
		foreach(self::$media_list as $k => $elem){
			$template_checker = new TemplateChecker($elem['include'], $elem['exclude']);
			if($template_checker->check($template)) {
				$show_meta_box = true;
				break;
			}
		}
		
		if($show_meta_box){
			add_meta_box(
				'wpu-media-manager',
				__( 'Media Manager', 'wtu_framework' ),
				array(__CLASS__, 'metabox_html'),
				$post_type,
				$context,
				$priority
			);
		}
	}
	
	/**
	 * Prints the HTML markup for the metabox
	 */
	public static function metabox_html($post){
		global $post;
		$template = get_post_meta($post->ID, '_wp_page_template', true);
		
		if(count(self::$media_list)){
			$is_first = true;
			foreach(self::$media_list as $k => $elem){
				$template_checker = new TemplateChecker($elem['include'], $elem['exclude']);
				if(!$template_checker->check($template)) continue;
				
				$name = self::META_KEY_NAME.'-'.$elem['id'];
				wp_nonce_field(__FILE__, $name.'_nonce');
				$value = get_post_meta($post->ID, $name, true);
				
				// main edit button
				$edit_button = HtmlHelper::anchor(
					'javascript:;', 
					HtmlHelper::span('', array('class'=>'wp-media-buttons-icon'))
						/*.sprintf(
							__('Manage Media for %s', 'wtu_framework'), 
							HtmlHelper::strong($elem['label'])
						),*/
						.__('Manage Media', 'wtu_framework'),
					array(
						'id'				=>	'wtu-media-manager-button-'.$elem['id'],
						'class'				=>	'button media-manager-button',
						'data-target'		=>	'#wtu-media-manager-element-'.$elem['id'],
						'data-target-undo'	=>	'#wtu-media-manager-undo-'.$elem['id'],
						'data-target-delete'=>	'#wtu-media-manager-delete-'.$elem['id'],
						'data-counter'		=>	'#wtu-media-manager-counter-'.$elem['id'],
						'data-frame-id'		=>	'wtu-media-manager-'.$elem['id'],
						'data-title'		=>	sprintf(__('Manage Media for %s', 'wtu_framework'), $elem['label']),
						'data-button-label'	=>	sprintf(__('Add selected media to %s set', 'wtu_framework'), $elem['label']),
						'data-multiple'		=>	'true',
						'data-elem-id'		=>	$elem['id'],
						'data-shortcode'	=>	$elem['shortcode'],
						'title'				=>	sprintf(__('Manage Media for %s', 'wtu_framework'), $elem['label'])
					)
				);
				
				// input to store temp values, use text instead of hidden to debug
				$input = HtmlHelper::input(
					$name, 
					'hidden', 
					array(
						'id'	=>	'wtu-media-manager-element-'.$elem['id'],
						'value'	=>	$value
					)
				);
				
				// Counter
				$number = 0;
				if(isset($value) && $value != '') $number = count(explode(',', $value));
				$counter = '&nbsp;'.HtmlHelper::span(
					($number > 0) 
						? sprintf(_n('1 element', '%s elements', $number, 'wtu_framework'), $number) 
						: __('Empty', 'wtu_framework'),
					array(
						'id'						=>	'wtu-media-manager-counter-'.$elem['id'],
						'data-label-no-images'		=>	__('Empty', 'wtu_framework'),
						'data-label-one-image'		=>	__('1 element', 'wtu_framework'),
						'data-label-more-images'	=>	__('%s elements', 'wtu_framework')
					)
				);
				
				// delete button
				$delete = HtmlHelper::anchor(
					'javascript:;', 
					//HtmlHelper::image('/wp-includes/js/tinymce/plugins/wpeditimage/img/delete.png'), 
					__('Delete', 'wtu_framework'),
					array(
						'id'				=>	'wtu-media-manager-delete-'.$elem['id'],
						'style'				=>	($value) ? '' : 'display:none;',
						'class'				=>	'delete-media-manager-gallery submitdelete', 
						'data-counter'		=>	'#wtu-media-manager-counter-'.$elem['id'],
						'data-gallery'		=>	$elem['id'],
						'data-target'		=>	'#wtu-media-manager-element-'.$elem['id'],
						//'data-label-undo'	=>	__('Undo', 'wtu_framework'),
						'data-target-undo'	=>	'#wtu-media-manager-undo-'.$elem['id'],
						'data-target-origin'=>	'#wtu-media-manager-button-'.$elem['id'],
						'title'				=>	__('Delete Media Set', 'wtu_framework'),
					)
				);
				
				// undo button
				$undo = HtmlHelper::anchor(
					'javascript:;', 
					__('Undo', 'wtu_framework'), 
					array(
						'id'				=>	'wtu-media-manager-undo-'.$elem['id'],
						'style'				=>	'display:none;',
						'class'				=>	'undo-media-manager-gallery', 
						'data-gallery'		=>	$elem['id'],
						'data-target'		=>	'#wtu-media-manager-element-'.$elem['id'],
						'data-elem-id'		=>	$elem['id'],
						'data-target-origin'=>	'#wtu-media-manager-button-'.$elem['id'],
						'data-target-delete'=>	'#wtu-media-manager-delete-'.$elem['id'],
						'data-counter'		=>	'#wtu-media-manager-counter-'.$elem['id'],
						//'data-label-undo'	=>	__('Undo', 'wtu_framework'),
						'title'				=>	__('Restore Media Set', 'wtu_framework'),
					)
				);
				
				$title = HtmlHelper::paragraph(HtmlHelper::strong($elem['label']));
				
				$inner_html = '';
				if(!$is_first) $inner_html .= HtmlHelper::br();
				$inner_html .= '<table class="widefat"><thead>';
				$inner_html .= '<td width="33%">'.$title.'</td>';
				$inner_html .= '<td width="33%">&nbsp;</td>';
				$inner_html .= '<td width="33%">'.$edit_button.'</td></thead>';
				$inner_html .= '<tbody><tr style="line-height: 25px;"><td class="submitbox">'.$delete.$undo.'</td>';
				$inner_html .= '<td>&nbsp;</td>';
				$inner_html .= '<td>'.$counter.'</td>';
				$inner_html .= '</tr></tbody></table>';
				$inner_html .= $input;
				
				echo HtmlHelper::div($inner_html, array('class'=>''));
				
				$is_first = false;
			}
			
		}
		//var_dump($value);
	}
	
	/**
	 * Saves the metabox data while saving the page
	 * @param int $post_id the post id
	 * @deprecated using ajax instead
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
		if(!isset($parms['shortcode'])) $parms['shortcode'] = $id;
		if(!isset($parms['wpml'])) $parms['wpml'] = array(
			'default_lang'			=>	true,
			'homepage'				=>	false,
			'homepage_default_lang'	=>	false
		);
		self::$media_list[$id] = $parms;
	}
	
	/**
	 * Retrieves the shortcode for the given set.
	 * @uses MediaManager::get_meta()
	 * @param string $set identifier of the set of elements
	 * @param int $post_id the post ID to be queried
	 * @return string the shortcode fot the media set
	 */
	public static function get_media($set, $post_id=null){
		if(is_null($post_id)) $post_id = get_the_ID();
		$data = self::get_meta($set, $post_id);
		return $data;
	}
	
	/**
	 * Utitlity to retrieve the post meta for the given media set.
	 * 
	 * If WPML is available and notthing is retrieved from the
	 * given $post_id, this method will search for the same
	 * post meta in the corresponding post in default language.
	 * 
	 * It is protected cause it can be overloaded by child classes
	 * but it's for internal use only. MediaManager::get_media() 
	 * is the public method.
	 * 
	 * @uses icl_object_id() (WPML) if available
	 * @param string $set identifier of the set of elements
	 * @param int $post_id the post ID to be queried
	 * @return string the meta
	 */
	protected static function get_meta($set, $post_id){
		$meta_name = self::META_KEY_NAME.'-'.sanitize_title($set);
		$data = get_post_meta($post_id, $meta_name, true);
		
		if(isset($GLOBALS['sitepress'])){
			global $sitepress;
			
			// look for the post in default language
			if(self::$media_list[$set]['wpml']['default_lang'] && empty($data)){
				$post_id = icl_object_id($post_id, get_post_type($post_id), true, $sitepress->get_default_language());
				if($post_id == 0) return;
				$data = get_post_meta($post_id, $meta_name, true);
			}			
			
			// look for the homepage in current language
			if(self::$media_list[$set]['wpml']['default_lang'] && empty($data)){
				$data = get_post_meta(get_option('page_on_front'), $meta_name, true);
			}		
			
			// look for the homepage in default language
			if(self::$media_list[$set]['wpml']['default_lang'] && empty($data)){
				$post_id = icl_object_id(get_option('page_on_front'), get_post_type(get_option('page_on_front')), true, $sitepress->get_default_language());
				if($post_id == 0) return;
				$data = get_post_meta($post_id, $meta_name, true);
			}		
		}		
			
		return $data;
	}
	
	/**
	 * Retrieves the markup for the given set
	 * @param string $set identifier of the set of elements
	 * @param int $post_id the post ID to be queried
	 * @return string html markup
	 */
	public static function get_gallery($set, $post_id=null){
		return do_shortcode(self::get_media($set, $post_id));
	}
	
}


