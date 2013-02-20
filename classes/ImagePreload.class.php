<?php 
/**
 * Contains ImagePreload class definition
 */



/**
 * Takes care of image preloading with jQuery imagesloaded
 * @author etessore
 * @version 1.0.0
 */
class ImagePreload extends GalleryHelper{
	
	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
	public function get_markup(){
		if(empty($this->images)){ return ''; }
		
		$this->load_assets();
		$toret = array();
		
		foreach($this->images as $k => $image){
			$tmp 			=	new stdClass();
			$tmp->src 		=	$this->get_image_src($k);
			$tmp->alt 		=	$this->get_image_alt($k);
			$tmp->desc 		=	$this->get_image_description($k);
			$tmp->caption 	=	$this->get_image_caption($k);
			$tmp->id		=	$this->get_image_id($k);
			$tmp->width		=	$this->get_image_width($k);
			$tmp->height	=	$this->get_image_height($k);
			$toret[]		=	$tmp;
		}
		
		$json = json_encode(
			array(
				'images'	=>	$toret,
				//'loading'	=>	$this->static_markup['loading'],
				'uid'		=>	$this->unid
			)
		);
		
		return <<< EOF
	<script>
		window.preload_images = window.preload_images || {};
		window.preload_images.{$this->unid} = $json;
	</script>
EOF;
	}
}