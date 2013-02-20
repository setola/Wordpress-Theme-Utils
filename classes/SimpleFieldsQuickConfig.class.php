<?php 
/**
 * Contains SimpleFieldsQuickConfig class definition
 */

/**
 * Perform a quick configuration for Simple Fields plugin
 * with the default values used by FastBooking
 * By default you only need to call in functions.php:
 * <code>new SimpleFieldsQuickConfig();</code>
 * @author etessore
 * @version 1.0
 */
class SimpleFieldsQuickConfig{
	
	/**
	 * @var string the post type
	 */
	private $post_type;
	
	/**
	 * @var array stores the Simple Fields groups
	 */
	private $groups;
	
	/**
	 * @var array Simple Fields connectors
	 */
	private $connectors;
	
	/**
	 * Initializes the default values
	 */
	public function __construct(){
		
		$post_type = array ( "0" => '', "page" => '__inherit__', ) ;
		
		$groups = array (
				"1" => array (
						"id" => '1',
						"name" => 'Map Data',
						"repeatable" => '1',
						"fields" => array (
								"1" => array (
										"name" => 'Center Latitude',
										"description" => 'ex: 40.123456',
										"type" => 'text',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '1', "deleted" => '0', ),
								"2" => array (
										"name" => 'Center Longitude',
										"description" => 'ex: 8.123456',
										"type" => 'text',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '2', "deleted" => '0', ),
								"3" => array (
										"name" => 'Latitude',
										"description" => 'ex: 40.123456',
										"type" => 'text',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '3', "deleted" => '0', ),
								"4" => array (
										"name" => 'Longitude',
										"description" => 'ex: 8.123456',
										"type" => 'text',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '4', "deleted" => '0', ),
								"5" => array (
										"name" => 'Balloon Title',
										"description" => 'The title of the pop-up',
										"type" => 'text',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '5', "deleted" => '0', ),
								"6" => array (
										"name" => 'Balloon Text',
										"description" => 'The content of the pop-up',
										"type" => 'textarea',
										"type_textarea_options" => array (
												"use_html_editor" => '1', ),
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '6', "deleted" => '0', ),
								"7" => array (
										"name" => 'Zoom',
										"description" => 'Zoom of the map',
										"type" => 'text',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '7', "deleted" => '0', ),
								"8" => array (
										"name" => 'Map Type',
										"description" => 'Type of the map',
										"type" => 'dropdown',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"type_dropdown_options" => array(
												"dropdown_num_1" => array( "value" => 'ROADMAP', "deleted" => '0'),
												"dropdown_num_2" => array( "value" => 'SATELLITE', "deleted" => '0'),
												"dropdown_num_3" => array( "value" => 'HYBRID', "deleted" => '0'),
												"dropdown_num_4" => array( "value" => 'TERRAIN', "deleted" => '0'),
										),
										"id" => '8', "deleted" => '0', ),
						),
						"deleted" => '',
						"description" => 'Contains map datas for creating pop-up in the google maps',
						"type_textarea_options" => array ( ),
						"type_radiobuttons_options" => array ( ),
						"type_taxonomy_options" => array ( ),
				),
				"2" => array (
						"id" => '2',
						"name" => 'Special Offers',
						"repeatable" => '',
						"fields" => array (
								"1" => array (
										"name" => 'Promo Code',
										"description" => 'Copy/paste promo code from the BE back-office',
										"type" => 'text',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '1', "deleted" => '0', ),
								"2" => array (
										"name" => 'Offer Subtitle',
										"description" => 'Catch phrase of your offer',
										"type" => 'textarea',
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '2', "deleted" => '0', ), ),
						"deleted" => '',
						"description" => '(Fill in only if the page is a special offer)',
						"type_textarea_options" => array ( ),
						"type_radiobuttons_options" => array ( ),
						"type_taxonomy_options" => array ( ),
				),
				"3" => array (
						"id" => '3',
						"name" => 'Subtitle',
						"repeatable" => '',
						"fields" => array (
								"1" => array (
										"name" => 'Subtitle',
										"description" => ' ',
										"type" => 'textarea',
										"type_textarea_options" => array (
												"use_html_editor" => '1', ),
										"type_post_options" => array (
												"additional_arguments" => '', ),
										"type_taxonomyterm_options" => array (
												"enabled_taxonomy" => '',
												"additional_arguments" => '', ),
										"id" => '1',
										"deleted" => '0',
								)
						),
						"deleted" => '',
						"description" => '',
						"type_textarea_options" => array ( ),
						"type_radiobuttons_options" => array ( ),
						"type_taxonomy_options" => array ( ),
				)
		);
		
		$connectors = array (
				"1" => array (
						"id" => '1',
						"name" => 'Page with Map',
						"field_groups" => array (
								"1" => array (
										"id" => '1',
										"name" => 'Map Data',
										"deleted" => '0',
										"context" => 'advanced',
										"priority" => 'high', ), ),
						"post_types" => array (
								"0" => 'page', ),
						"deleted" => '',
						"hide_editor" => '', ),
				"2" => array (
						"id" => '2',
						"name" => 'Page with Offer',
						"field_groups" => array (
								"2" => array (
										"id" => '2',
										"name" => 'Special Offers',
										"deleted" => '0',
										"context" => 'advanced',
										"priority" => 'high', ),
						),
						"post_types" => array (
								"0" => 'page', ),
						"deleted" => '',
						"hide_editor" => '', ),
				"3" => array (
						"id" => '3',
						"name" => 'Full Options',
						"field_groups" => array (
								"2" => array (
										"id" => '2',
										"name" => 'Special Offers',
										"deleted" => '0',
										"context" => 'normal',
										"priority" => 'low', ),
								"1" => array (
										"id" => '1',
										"name" => 'Map Data',
										"deleted" => '0',
										"context" => 'normal',
										"priority" => 'low', ),
						),
						"post_types" => array (
								"0" => 'page', ),
						"deleted" => '',
						"hide_editor" => '',
				),
				"4" => array (
						"id" => '4',
						"name" => 'Page with Subtitle',
						"field_groups" => array (
								"3" => array (
										"id" => '3',
										"name" => 'Subtitle',
										"deleted" => '0',
										"context" => 'normal',
										"priority" => 'low', )
						),
						"post_types" => array (
								"0" => 'page', ),
						"deleted" => '',
						"hide_editor" => '', )
		);
		
		$this
			->set_connectors($connectors)
			->set_groups($groups)
			->set_post_type($post_type)
			->check_config();
	}
	
	/**
	 * Set the post type to the given parameter
	 * @param array $post_type the post type
	 */
	public function set_post_type($post_type){
		$this->post_type = $post_type;
		return $this;
	}
	
	/**
	 * Set the post groups to the given parameter
	 * @param array $groups the groups
	 */
	public function set_groups($groups){
		$this->groups = $groups;
		return $this;
	}
	
	/**
	 * Set the post connectors to the given parameter
	 * @param array $connectors the post connectors
	 */
	public function set_connectors($connectors){
		$this->connectors = $connectors;
		return $this;
	}
	
	/**
	 * Check if a config for the Simple Fields plugin exists,
	 * elsewhere it will add the default one.
	 * For better performance this check is limited
	 * only to the admin pages, not to the public pages.
	 * So if you wanna add the default values you'll have to visit wp-admin 
	 */
	function check_config() {
		if( ! is_admin() ){
			return $this;
		}
		if ( ! get_option('simple_fields_post_type_defaults',false)) {
			update_option('simple_fields_post_type_defaults', $this->post_type);
		}
		if ( ! get_option('simple_fields_groups',false)) {
			update_option('simple_fields_groups', $this->groups);
		}
		if ( ! get_option('simple_fields_post_connectors',false)) {
			update_option('simple_fields_post_connectors', $this->connectors);
		}

		return $this;
	}
}