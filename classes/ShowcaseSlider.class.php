<?php 
/**
 * Definition Class for ShowcaseSlider
 */



/**
 *
 * @author etessore
 * @version 1.0.0
 * @package classes
 * @subpackage image manager
 */
class ShowcaseSlider extends GalleryHelper{

	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
	public function get_markup(){

		if(empty($this->images)){
			return '';
		}

		ThemeHelpers::load_js('jquery.showcase');
		ThemeHelpers::load_js('slideshow-oneimageforall');
		// vd($this->images);
		$toret = '';
		foreach($this->images as $id => $image) {

			$tp2 = '<div class="showcase-slide">
					<div class="showcase-content">
					<div class="showcase-content-wrapper">
					<img  class="" title=":title:" alt=":alt:" src=":src:?h=:height:&zc=1" style="height: :height:px">
					</div>
					</div>
					</div>';


			$class = '';

			// for big image
			$subs1 = array(
					':class:'		=> 	$class,
					':src:'			=>	$this->get_image_src($id),
					':alt:'			=>	$this->get_image_alt($id),
					':width:'		=>	$this->get_image_width($id),
					':height:'	=>	$this->get_image_height($id),
					':caption:'	=> 	$this->get_image_caption($id)
			);
			$toret .= str_replace(array_keys($subs1), array_values($subs1), $tp2);
		}
		
		
		return '<div id="showcase" class="showcase">'.$toret.'</div>';

	}
}