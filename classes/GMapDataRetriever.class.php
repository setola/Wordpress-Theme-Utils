<?php 
/**
 * Retrieves GMaps data for the gmap.js integration
 * @author etessore
 * @version 1.0
 */
class GMapDataRetriever {
	private $map_data;
	
	/**
	 * 
	 */
	public function __construct(){
		//$this->use_simple_fields();
	}
	
	/**
	 * Set the map data to the given set
	 * @param array $map_data the map data
	 * @return GMapDataRetriever $this for chainability
	 */
	public function set_map_data($map_data){
		$this->map_data = $map_data;
		return $this;
	}
	
	/**
	 * Set the class to get map datas from the Simple fields plugin
	 * Checks if the plugin is enabled.
	 * @return GMapDataRetriever $this for chainability
	 */
	public function use_simple_fields(){
		if(!function_exists('simple_fields_get_post_group_values')){
			wp_die('You need Simple Fields to be Up And Running!');
		}
		
		$map_data = simple_fields_get_post_group_values(get_the_ID(),'Map Data');
		return $this
			->set_map_data(
				array(
					'center'	=>	array(
						'lat' 		=>	floatval(
							empty($map_data['Center Latitude'][0])
							? $map_data['Latitude'][0]
							: $map_data['Center Latitude'][0]
						),
						'lng'		=>	floatval(
							empty($map_data['Center Longitude'][0])
							? $map_data['Longitude'][0]
							: $map_data['Center Longitude'][0]
						)
					),
					'point'		=>	array(
						'lat'		=>	floatval($map_data['Latitude'][0]),
						'lng'		=>	floatval($map_data['Longitude'][0])
					),
					'zoom'		=>	intval($map_data['Zoom'][0]),
					'type'		=>	$map_data['Map Type'][0],
					'title'		=>	$map_data['Balloon Title'][0],
					'content'	=>	str_replace(
						array('%book%'),
						array('<a class="book-action" href="javascript:;">'.__('book','theme').'</a>'),
						$map_data['Balloon Text'][0]
					),
					'book_trans'	=>	__('book','theme')
				)
			);
	}
	
	public function use_shared_datastore(){
		
	}
	
	
	
	function get_script_content(){
		return HtmlHelper::script('var map_info = ' . json_encode($this->map_data));
	}
	
	function the_script(){
		echo $this->get_script_content();
	}
}


