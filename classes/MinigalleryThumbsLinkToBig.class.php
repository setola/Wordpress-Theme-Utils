<?php 
/**
 * storest MinigalleryThumbsLinkToBig class definition
 */

/**
 * Generates a list of thumbnail each one is an anchor 
 * to the image with 'full' media dimensions.
 * By default loads FancyBox so to have an eye candy popup effect.
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class MinigalleryThumbsLinkToBig extends GalleryHelper{
	/**
	 * @var string the substitution template
	 */
	public $tpl;
	
	/**
	 * Initializes the minigallery
	 */
	public function __construct(){
		$this->set_template('<div class="images-container">%list%</div>');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
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
					array(
							'class'	=>	'fancybox',
							'rel'	=>	'group',
							'title'	=>	$this->get_image_caption($index)
					)
				);
			}
			$toret = $subs->set_markup('list', $toret)->replace_markup();
		}
		return $toret;
	}
}