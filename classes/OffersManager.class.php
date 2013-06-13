<?php 

/**
 * Definition of OffersManager feature
 */


/**
 * Manage the Offers linked to the booking engine by promo code
 */
class OffersManager{
	/**
	 * Stores the custom post name
	 * @var string
	 */
	const CUSTOM_POST_NAME 	= 'offer';

	/**
	 * Stores the custom taxonomy name
	 * @var string
	 */
	const CUSTOM_TAX_NAME 	= 'offer-type';

	/**
	 * Stores the meta key name where store the offers details
	 * @var string
	 */
	const META_KEY_NAME		= '_wtu_offers_details';

	/**
	 * Use to retrieve the book now button compatible with Fblib
	 * @var int
	 */
	const FORMAT_OOFBLIB	=	1;

	/**
	 * Use to retrieve the book now button compatibile with fblib.js
	 * @var int
	 */
	const FORMAT_FBLIB		=	2;

	/**
	 * Stores the status of the feature
	 * @var bool
	 */
	public static $enabled = false;

	/**
	 * Stores the connect name
	 * @var string
	 */
	public static $connect_name;

	/**
	 * Returns the list of allowed offers details
	 * @return array
	 */
	private static function offer_details_list(){
		return array(
				'cname'		=>	array(
						'name' 		=>	'cname',
						'label'		=>	__('Connect Name', 'wtu_framework')
				),
				'lg'		=>	array(
						'name'		=>	'lg',
						'label'		=>	__('Language', 'wtu_framework')
				),
				'codeprice'	=>	array(
						'name'		=>	'codeprice',
						'label'		=>	__('Price Code', 'wtu_framework')
				),
				'firstroom'	=>	array(
						'name'		=>	'firstroom',
						'label'		=>	__('First Room', 'wtu_framework')
				),
				'codetrack'	=>	array(
						'name'		=>	'codetrack',
						'label'		=>	__('Track Code', 'wtu_framework')
				),
				'firstdate'	=>	array(
						'name'		=>	'firstdate',
						'label'		=>	__('First Date', 'wtu_framework')
				),
				'price'		=>	array(
						'name'		=>	'price',
						'label'		=>	__('Price', 'wtu_framework')
				),
				'price_old'	=>	array(
						'name'		=>	'price_old',
						'label'		=>	__('Price Old', 'wtu_framework')
				)
		);
	}

	/**
	 * Enables the feature
	 *
	 * @param string $connect_name the default connect name for these offers
	 */
	public static function enable($connect_name=''){
		self::$connect_name = $connect_name;
		add_action('init', array(__CLASS__, 'register_custom_post'));
		add_action('init', array(__CLASS__, 'register_custom_taxonomy'));
		add_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		add_action('save_post', array(__CLASS__, 'save_metabox_data'));
		self::$enabled = true;
	}

	/**
	 * Disables the feature
	 */
	public static function disable(){
		remove_action('init', array(__CLASS__, 'register_custom_post'));
		remove_action('init', array(__CLASS__, 'register_custom_taxonomy'));
		remove_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
		remove_action('save_post', array(__CLASS__, 'save_metabox_data'));
		self::$enabled = false;
	}

	/**
	 * Register the custom post type
	 */
	public static function register_custom_post(){
		register_post_type(
		self::CUSTOM_POST_NAME,
		array(
		'label' 				=> __('Offers', 'wtu_framework'),
		'description' 			=> __('A special offer for your hotel', 'wtu_framework'),
		'public' 				=> true,
		'show_ui' 				=> true,
		'show_in_menu' 			=> true,
		'capability_type' 		=> 'post',
		'hierarchical' 			=> false,
		'rewrite' 				=> array('slug' => ''),
		'query_var' 			=> true,
		'exclude_from_search' 	=> false,
		'supports' 				=> array(
		'title',
		'editor',
		'excerpt',
		'trackbacks',
		'custom-fields',
		'comments',
		'revisions',
		'thumbnail',
		'author',
		'page-attributes',
		),
		'labels' => array (
		'name' 					=> __('Offers', 'wtu_framework'),
		'singular_name' 		=> __('Offer', 'wtu_framework'),
		'menu_name' 			=> __('Offers', 'wtu_framework'),
		'add_new' 				=> __('Add Offer', 'wtu_framework'),
		'add_new_item' 			=> __('Add New Offer', 'wtu_framework'),
		'edit' 					=> __('Edit', 'wtu_framework'),
		'edit_item' 			=> __('Edit Offer', 'wtu_framework'),
		'new_item' 				=> __('New Offer', 'wtu_framework'),
		'view' 					=> __('View Offer', 'wtu_framework'),
		'view_item' 			=> __('View Offer', 'wtu_framework'),
		'search_items' 			=> __('Search Offers', 'wtu_framework'),
		'not_found' 			=> __('No Offers Found', 'wtu_framework'),
		'not_found_in_trash' 	=> __('No Offers Found in Trash', 'wtu_framework'),
		'parent' 				=> __('Parent Offer', 'wtu_framework'),
		),
		)
		);
	}

	/**
	 * Registers the needed custom taxonomy
	 */
	public static function register_custom_taxonomy(){
		register_taxonomy(
		self::CUSTOM_TAX_NAME,
		array (0 => self::CUSTOM_POST_NAME),
		array(
		'hierarchical' 		=> true,
		'label' 			=> __('Types', 'wtu_framework'),
		'show_ui' 			=> true,
		'query_var' 		=> true,
		'rewrite' 			=> array('slug' => ''),
		'singular_label' 	=> __('Type', 'wtu_framework')
		)
		);
	}

	/**
	 * Registers the needed metaboxes
	 */
	public static function register_metaboxes(){
		add_meta_box(
		'wpu-offers-manager',
		__( 'Offer Detail', 'wtu_framework' ),
		array(__CLASS__, 'metabox_html'),
		self::CUSTOM_POST_NAME,
		'side',
		'high'
				);
	}

	/**
	 * Prints the metabox for the offer details
	 * @param object $post the current post
	 */
	public static function metabox_html($post){
		wp_nonce_field(__FILE__, self::META_KEY_NAME.'_nonce');

		$values = get_post_meta($post->ID, self::META_KEY_NAME, true);

		echo '<table class="form-table">';
		foreach(self::offer_details_list() as $k => $v){
			$name = self::META_KEY_NAME.'['.$v['name'].']';
			echo '<tr>';
			echo '<th>'.HtmlHelper::label($v['label'], $name).'</th>';
			echo '<td>'.HtmlHelper::input(
					$name,
					'text',
					array('value' => $values[$k])
			).'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	/**
	 * Saves the metabox data while saving the offer
	 */
	public static function save_metabox_data($post_id){
		// First we need to check if the current user is authorised to do this action.
		$post_obj = get_post_type_object($_POST['post_type']);
		if(!current_user_can($post_obj->cap->edit_post, $post_id)) return;

		// Secondly we need to check if the user intended to change this value.
		if(
		!isset($_POST[self::META_KEY_NAME.'_nonce'])
		|| !wp_verify_nonce($_POST[self::META_KEY_NAME.'_nonce'], __FILE__)
		) return;

		$sanitized = array();

		$cname = self::offer_details_list();
		$cname = $cname['cname']['name'];
		if(empty($_POST[self::META_KEY_NAME][$cname]))
			$_POST[self::META_KEY_NAME][$cname] = self::$connect_name;

		foreach($_POST[self::META_KEY_NAME] as $k => $v){
			$sanitized[$k] = sanitize_text_field($v);
		}

		update_post_meta(
		$_POST['post_ID'],
		self::META_KEY_NAME,
		$sanitized
		);
	}

	/**
	 * Retrieves the offer details
	 *
	 * Format:
	 * 	array (
	 * 		'cname' 	=> 'ITAOSHTLExpress',
	 * 		'lg' 		=> '',
	 * 		'codeprice' => 'PROMO-Terme-e-gastronomia',
	 * 		'firstroom' => 'Double-Superior',
	 * 		'codetrack' => '',
	 * 		'firstdate' => '',
	 * 	)
	 * @param object $post the post object
	 * @return array a list of details
	 */
	public static function get_offer_details($post=null){
		if(is_null($post)) global $post;
		return get_post_meta($post->ID, self::META_KEY_NAME, true);
	}

	/**
	 * Retrieves the book now onclick attribute
	 * @param object $post the offer custom post
	 * @param number $format the format
	 * @return string the onclick attribute
	 */
	public static function get_booknow($post=null, $format=0){
		if(!self::$enabled) return;

		$values = self::get_offer_details($post);
		switch($format){
			case self::FORMAT_OOFBLIB:
			default:
				$toret = <<<EOF
Fblib.hhotelResaDirect('{$values['cname']}', '{$values['lg']}', '{$values['codeprice']}', '{$values['firstroom']}', '{$values['codetrack']}', '{$values['firstdate']}');
EOF;
				break;
					
			case self::FORMAT_FBLIB:
				$toret = <<<EOF
hhotelResaDirect('{$values['cname']}', '{$values['lg']}', '{$values['codeprice']}', '{$values['firstroom']}', '{$values['codetrack']}', '{$values['firstdate']}');
EOF;
				break;
		}
		return $toret;
	}

	/**
	 * Retrieves the price_content div
	 * @return string
	 */
	public static function get_offer_price(){
		$values = self::get_offer_details($post);

		$from = __('From', 'theme');
		$currency = 'EUR';
		$per_night = __('per night', 'theme');


		return <<< EOF
		<div class="price_content">
			<span class="price_apd">$from</span>
			<span class="price_rate_strike">{$values['price_old']}</span>
			<span class="price_rate">{$values['price']}</span>
			<span class="price_currency">$currency</span>
			<span class="price_pn">$per_night</span>
		</div>
EOF;
	}

}
