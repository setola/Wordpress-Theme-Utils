<?php 

class MinigalleryThumbsLinkToBig extends GalleryHelper{
	public $tpl;
	
	public function __construct(){
		$this->set_template('<div class="images-container">%list%</div>');
	}
	
	public function get_markup(){
		$toret = '';
		if(count($this->images)>0){
			$subs = new SubstitutionTemplate();
			$subs->set_tpl($this->tpl);
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
			$toret = $subs->set_markup('list', $toret)->replace_markup();
		}
		return $toret;
	}
}