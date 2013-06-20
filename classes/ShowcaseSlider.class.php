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
	 * @see FeatureWithAssets::load_assets()
	 */
	function load_assets(){
		ThemeHelpers::load_js('showcase');
	}

	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
	public function get_markup(){

		if(empty($this->images)){
			return '';
		}
		
		$toret = '';
		
		$subs = new SubstitutionTemplate();
		$subs->set_tpl(<<<EOF
	<div class="showcase-slide">
		<div class="showcase-content">
			<div class="showcase-content-wrapper">
				%img%
			</div>
		</div>
	</div>	
EOF
		);		
		foreach($this->images as $id => $image) {
			$toret .= $subs
				->set_markup('img', wp_get_attachment_image($this->get_image_id($id), $this->media_dimension))
				->replace_markup();
		}
		
		
		return '<div id="showcase" class="showcase">'.$toret.'</div>';

	}
}