<?php 
/**
 * Manages multiple hotels in a single website
 * 
 * @author mcanever
 * @version 1.0.0
 *
 */
class HotelManager {
	/**
	 * Maintains the list of othels
	 * @var array
	 */
	public $hotels=array();
	
	/**
	 * Maintains the status of this feature
	 * @var unknown
	 */
	public static $enabled = false;
	
	/**
	 * Stores the list of errors
	 * @var array
	 */
	private $errors=array();
	
	/**
	 * Stores the current hotel
	 * @var Hotel
	 */
	private $current_hotel=null;
	
	/**
	 * Postmeta name to identify a single hotel
	 * @var string
	 */
	const META_KEY_NAME = '_this_is_an_hotel';
	
	/**
	 * Enable the feature
	 */
	public static function enable() {
		add_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		add_action('save_post', array(__CLASS__, 'save_metabox_data'));
		self::$enabled = true;
	}
	
	/**
	 * Disables the feature
	 */
	public static function disable(){
		remove_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		remove_action('save_post', array(__CLASS__, 'save_metabox_data'));
		self::$enabled = false;
	}
	
	/**
	 * Registers the 'Is This An Hotel?' metabox
	 */
	public static function register_metaboxes(){
		add_meta_box(
			'wpu-hotel-manager',
			__( 'Is this page an Hotel?', 'wtu_framework' ),
			array(__CLASS__, 'metabox_html'),
			'page',
			'side',
			'high'
		);
	}
	
	/**
	 * Prints the HTML markup for the 'Is This An Hotel?' metabox
	 */
	public static function metabox_html($post){
		wp_nonce_field(__FILE__, self::META_KEY_NAME.'_nonce');
		$value = get_post_meta($post->ID, self::META_KEY_NAME, true);
		echo HtmlHelper::input(
			self::META_KEY_NAME, 
			'checkbox', 
			array('checked' => ($value=='on') ? 'checked' : '')
		);
		echo '&nbsp;'.HtmlHelper::label(__('Yes, this page is an Hotel!', 'wtu_framework'), self::META_KEY_NAME);
	}
	
	/**
	 * Saves the metabox data while saving the page
	 */
	public static function save_metabox_data($post_id){
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
		
		update_post_meta(
			$_POST['post_ID'], 
			self::META_KEY_NAME, 
			sanitize_text_field($_POST[self::META_KEY_NAME])
		);
	}
	
	/**
	 * Get all Hotels in the current website
	 * @return array a list of posts that are main hotel page
	 */
	public static function get_hotels(){
		return get_posts(
			array(
				'meta_key'		=>	self::META_KEY_NAME ,
				'meta_value'	=>	'on',
				'post_type'		=>	'page'
			) 
		);
	}
	
	/**
	 * Retrieves the hotels page ids
	 * @return array list of ids
	 */
	public static function get_hotels_ids(){
		global $wpdb;
		return $wpdb->get_col('SELECT post_id FROM  `'.$wpdb->postmeta.'` WHERE  `meta_key` = \''.self::META_KEY_NAME.'\'');
	}
	
	/**
	 * Merges post anchestors and post id
	 * @param object $post
	 * @return array:
	 */
	protected static function get_haystack($post=null){
		if(empty($post)) global $post;
		return array_merge((array) get_post_ancestors($post), (array) $post->ID);
	}
	
	/**
	 * Tests if the given $post is from the group website
	 * @param int|object $post the post you need to check, default is the current one
	 * @return boolean true if $post is from group website
	 */
	public static function is_group($post=null){
		return is_null(self::get_hotel_id($post));
	}
	
	/**
	 * Test if the given $post is from a single hotel
	 * @param int|object $post the post you need to check, default is the current one
	 * @return boolean true if $post is from the hotel website
	 */
	public static function is_hotel($post=null, $hotel_name=null){
		return !self::is_group($post);
	}
	
	/**
	 * 
	 * @param string $post
	 * @return Ambigous <multitype:, multitype:Ambigous <string, NULL> >
	 */
	public static function get_hotel($post=null){
		return get_post(self::get_hotel_id($post));
	}
	
	/**
	 * Retrieves the hotel title for the given $post
	 * @param int|object $post the post you want to know
	 * @return string the hotel title
	 */
	public static function get_hotel_title($post=null){
		return get_the_title(self::get_hotel_id($post));
	}
	
	/**
	 * Retrieves the hotel slug
	 * @param string $post the slug
	 */
	public static function get_hotel_slug($post=null){
		$p = get_post(self::get_hotel_id($post));
		return $p->post_name;
	}
	
	/**
	 * Retrieves the hotel post id
	 * @param int|object $post the post you want to check
	 * @param boolean $default_language true if you want the id of the default language translation
	 * @return int the hotel post id
	 */
	public static function get_hotel_id($post=null, $default_language=false){
		if(empty($post)) global $post;
		
		$haystack = self::get_haystack($post);
		
		foreach(self::get_hotels_ids() as $id){
			if(in_array($id, $haystack)){
				global $sitepress;
				if(isset($sitepress) && $default_language){
					return icl_object_id($id, get_post_type($id), true, $sitepress->get_default_language());
				}
				return $id;
			}
		}
	}
	
}


// TODO
class Hotel {
	public $hasPages=false;
	public $systag=false;
	private $errors=array();
	
	public function __construct($systag, $data=array()) {
		$this->systag=$systag;
		$this->check_for_pages();
		foreach ($data as $k => $v) {
			if (is_array($v)) $v=(Object)$v;
			$this->{$k}=$v;
		}
		$this->add_book_link();
	}
	public function check_for_pages() {
		$tmp=fb_get_posts_with_systag_fixed($this->systag);
		if (count($tmp) == 1) {
			$this->hasPages=true;
			$this->postID=$tmp[0]->ID;
		} else {
			$this->hasPages=false;
			$this->postID=null;
			$this->errors[]="[HOTEL] no pages found with Systag $systag";
		}
	}
	public function debug() {
		foreach ($this->errors as $k=>$error) {
			echo "[$k] $error [/$k]\r\n";
		}		
	}
	private function add_book_link() {
		if (isset($this->cname)) {
			$this->booklink='<a onclick="if (typeof _gaq != \'undefined\') {_gaq.push([\'_trackPageview\', \'/FastBooking/ClicBook\']);} window.open(\'http://www.fastbookings.biz/DIRECTORY/dispoprice.phtml?showPromotions=3&amp;clusterName='.$this->cname.'&amp;Hotelnames='.$this->cname.'\',\'reservation\',\'toolbar=no,width=1000,height=700,menubar=no,scrollbars=yes,resizable=yes,alwaysRaised=yes\');" href="javascript:;" class="book-action">'.
					__('book', 'theme').
				'</a>';	
		}
	}
}