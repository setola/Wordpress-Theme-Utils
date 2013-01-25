<?php 

class MinigalleryThumbsLinkToBig extends GalleryHelper{
	public function get_markup(){
		$toret = '';
		if(count($this->images)>0){
			ThemeHelpers::load_js('minigallery-thumbs-link-to-big');
			ThemeHelpers::load_css('jquery-fancybox');
			foreach($this->images as $index => $image){
				$toret .= HtmlHelper::anchor(
					$this->set_wp_media_dimension('full')->get_image_src($index), 
					HtmlHelper::image(
						wp_get_attachment_thumb_url($image->ID),
						array(
							'alt'			=>	$this->get_image_alt($index),
							'title'			=>	$this->get_image_description($index),
							'data-caption'	=>	$this->get_image_caption($index)
						)
					), 
					array('class'=>'fancybox','rel'=>'group')
				);
			}
		}
		return $toret;
	}
}