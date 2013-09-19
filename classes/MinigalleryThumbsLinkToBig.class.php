<?php 
/**
 * storest MinigalleryThumbsLinkToBig class definition
 */

/**
 * Generates a list of thumbnail each one is an anchor 
 * to the image with 'full' media dimensions.
 * By default loads FancyBox so to have an eye candy popup effect.
 * @author etessore
 * @version 1.0.1
 * @package classes
 */
class MinigalleryThumbsLinkToBig extends GalleryHelper{
	/**
	 * @var string the substitution template
	 */
	public $tpl;
	
	/**
	 * @var string stores the media dimension for the big image
	 */
	public $media_dimension_big;
	
	/**
	 * Initializes the minigallery
	 */
	public function __construct(){
		$this
			->set_template('%prev%<div class="images-container">%list%</div>%next%')
			->set_wp_media_dimension_big();
	}
	
	/**
	 * Sets the media dimension for the big image
	 * @param string $dimension the media dimension name
	 * @return MinigalleryThumbsLinkToBig $this for chainability
	 */
	public function set_wp_media_dimension_big($dimension='full'){
		$this->media_dimension_big = $dimension;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see GalleryHelper::get_markup()
	 */
	public function get_markup(){
		$toret = '';
		if(count($this->images)>0){
			$subs = new SubstitutionTemplate();
			$subs
				->set_tpl($this->tpl)
				->set_markup('prev', HtmlHelper::anchor('javascript:;', '&lt;', array('class'=>'prev control')))
				->set_markup('next', HtmlHelper::anchor('javascript:;', '&gt;', array('class'=>'next control')));
			
			ThemeHelpers::load_js('minigallery-thumbs-link-to-big');
			ThemeHelpers::load_css('jquery-fancybox');
			
			foreach($this->images as $index => $image){
				$image_big = wp_get_attachment_image_src($this->get_image_id($index), $this->media_dimension_big);
				$image_small = wp_get_attachment_image_src($this->get_image_id($index), $this->media_dimension);
				
				$toret .= HtmlHelper::anchor(
					$image_big[0], 
					HtmlHelper::image(
						$this->get_image_src($index),
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