<?php 

/**
 * Manages the markup for a generic set of images
 * Extend this with your nice and candy classes!
 * @author etessore
 * @version 1.0.0
 */
abstract class GalleryHelper{
	public $textdomain = 'theme';

	/**
	 * @var string the unique id for the current gallery
	 * it will be automatically created if not specified
	 */
	public $unid = false;

	/**
	 * @var String the html template
	 */
	public $tpl;

	/**
	 * @var array the list of images
	 */
	public $images = array();

	/**
	 * @var array Stores some static html
	 */
	public $static_markup;


	/**
	 * Set the html template for this gallery
	 * @param string $tpl the template
	 * @return GalleryHelper $this for chainability
	 */
	public function set_template($tpl){
		$this->tpl = $tpl;
		return $this;
	}

	/**
	 * Set the static markup; ie: prev\next\loading divs
	 * @param string $markup html markup
	 * @return GalleryHelper $this for chainability
	 */
	public function set_markup($key, $markup){
		$this->static_markup[$key] = $markup;
		return $this;
	}

	/**
	 * Adds an image to the current set
	 * @param string|int $img the image: if is an int it
	 * will be retrieved from the wp media, elsewhere it is an html tag
	 * @return GalleryHelper $this for chainability
	 */
	public function add_image($img){
		$this->images[] = $img;
		return $this;
	}

	/**
	 * Add some images to the current set
	 * @param array $images the list of images to be added
	 * @return GalleryHelper $this for chainability
	 */
	public function add_images($images){
		foreach($images as $img){
			$this->add_image($img);
		}
		return $this;
	}

	/**
	 * Loads the needed assets
	 * @return GalleryHelper $this for chainability
	 */
	public function load_assets(){
		return $this;
	}

	/**
	 * Calculates the unique id for the current gallery
	 * @return GalleryHelper $this for chainability
	 */
	public function calculate_unique(){
		$this->unid = uniqid('gallery-');
		return $this;
	}

	/**
	 * Checks if the $index image of the list is
	 * a wordpress media id or an image object
	 * @returns the src attribute for the $index image of the set
	 * @param int $index the index of the images list
	 */
	protected function get_image_src($index){
		$toret = $this->images[$index];

		if(is_integer($this->images[$index])){
			$toret = wp_get_attachment_url($this->images[$index]);
		} elseif(is_object($this->images[$index])){
			$toret = wp_get_attachment_url($this->images[$index]->ID);
		}

		return $toret;
	}

	/**
	 * @return the markup for the current gallery
	 */
	abstract public function get_markup();

	/**
	 * Echoes the markup for the current gallery
	 */
	public function the_markup(){
		echo $this->get_markup();
	}

}