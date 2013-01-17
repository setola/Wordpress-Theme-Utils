<?php 

/**
 * Manages the markup for a generic set of images
 * Extend this with your nice and candy classes!
 * @author etessore
 * @version 1.0.2
 */
abstract class GalleryHelper extends FeatureWithAssets{
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
	 * 
	 * @var array options for timthumb
	 */
	public $timthumb_opts;
	
	/**
	 * @var string WordPress media size name
	 */
	public $media_dimension;
	
	/**
	 * Set the unique id for this gallery
	 * @param string $unid the unique
	 * @return GalleryHelper $this for chainability
	 */
	public function set_uid($unid){
		$this->unid = $unid;
		return $this;
	}
	
	/**
	 * Set the timthumb options
	 * If set_wp_media_dimension() is called it will prevale on this.
	 * @param array $options
	 */
	public function set_timthumb_options($options){
		$this->timthumb_opts = $options;
		return $this;
	}
	
	/**
	 * Sets the dimension for this gallery.
	 * If set this option will prevale on timthumb
	 * @param string $dimension WordPress media dimension name
	 * @return GalleryHelper $this for chainability
	 */
	public function set_wp_media_dimension($dimension){
		$this->media_dimension = $dimension;
		return $this;
	}

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
	 * Replaces the markup in $this->tpl %tag%s with the one
	 * in the corresponding value of $this->static_markup[tag].
	 */
	public function replace_markup(){
		return str_replace(
			array_map(
				create_function('$k', 'return "%".$k."%";'), 
				array_keys($this->static_markup)
			),
			array_values($this->static_markup),
			$this->tpl
		);
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
		if(count($images)){
			foreach($images as $img){
				$this->add_image($img);
			}
		}
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
			//$toret = wp_get_attachment_url($this->images[$index]);
			$image = wp_get_attachment_image_src($this->images[$index], $this->media_dimension);
			$toret = $image[0];
		} elseif(is_object($this->images[$index])){
			//$toret = wp_get_attachment_url($this->images[$index]->ID);
			$image = wp_get_attachment_image_src($this->images[$index]->ID, $this->media_dimension);
			$toret = $image[0];
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['src']))
				$toret = $this->images[$index]['src'];
		}
		
		if($this->timthumb_opts && empty($this->media_dimension)){
			if(is_array($this->timthumb_opts) && !isset($this->timthumb_opts['render'])){
				$this->timthumb_opts['render'] = http_build_query($this->timthumb_opts);
			}
				
			$toret = $toret.'?'.$this->timthumb_opts['render'];
		}

		return $toret;
	}
	
	/**
	 * Get the width for the n-th image of the list
	 * @param int $index the index of the images list
	 */
	protected function get_image_width($index){
		if(isset($this->timthumb_opts['w'])){
			return $this->timthumb_opts['w'];
		} elseif(is_int($this->images[$index])){
			$image = wp_get_attachment_image_src($this->images[$index], $this->media_dimension);
			if(!empty($image[1])){
				return $image[1];
			}
		} elseif(is_object($this->images[$index])){
			$image = wp_get_attachment_image_src($this->images[$index]->ID, $this->media_dimension);
			if(!empty($image[1])){
				return $image[1];
			}
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['width']))
				return $this->images[$index]['width'];
		}
		return '100%';
	}

	/**
	 * Get the height for the n-th image of the list
	 * @param int $index the index of the images list
	 */
	protected function get_image_height($index){
		if(isset($this->timthumb_opts['h'])){
			return $this->timthumb_opts['h'];
		} elseif(is_int($this->images[$index])){
			$image = wp_get_attachment_image_src($this->images[$index], $this->media_dimension);
			if(!empty($image[2])){
				return $image[2];
			}
		} elseif(is_object($this->images[$index])){
			$image = wp_get_attachment_image_src($this->images[$index]->ID, $this->media_dimension);
			if(!empty($image[2])){
				return $image[2];
			}
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['height']))
				return $this->images[$index]['height'];
		}
		return '100%';
	}
	
	/**
	 * Checks if the $index image of the list is
	 * a wordpress media id or an image object
	 * @returns the id attribute for the $index image of the set
	 * @param int $index the index of the images list
	 */
	protected function get_image_id($index){
		if(is_integer($this->images[$index])){
			return $index;
		} elseif(is_object($this->images[$index])){
			return $this->images[$index]->ID;
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['id']))
				return $this->images[$index]['id'];
		}
	}
	
	/**
	 * Checks if the $index image of the list is
	 * a wordpress media id or an image object
	 * @returns the alt attribute for the $index image of the set
	 * @param int $index the index of the images list
	 */
	protected function get_image_alt($index){
		$toret = $this->images[$index];

		if(is_integer($this->images[$index])){
			$toret = get_post_meta($this->images[$index], '_wp_attachment_image_alt', true);
		} elseif(is_object($this->images[$index])){
			$toret = get_post_meta($this->images[$index]->ID, '_wp_attachment_image_alt', true);
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['alt']))
				$toret = $this->images[$index]['alt'];
		}

		return $toret;
	}
	
	/**
	 * Checks if the $index image of the list is
	 * a wordpress media id or an image object
	 * @returns the caption for the $index image of the set
	 * @param int $index the index of the images list
	 */
	protected function get_image_caption($index){
		$toret = $this->images[$index];

		if(is_integer($this->images[$index])){
			$post = get_post($this->images[$index]);
			$toret = $post->post_excerpt;
		} elseif(is_object($this->images[$index])){
			$toret = $this->images[$index]->post_excerpt;
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['caption']))
				$toret = $this->images[$index]['caption'];
		}

		return $toret;
	}
	
	/**
	 * Checks if the $index image of the list is
	 * a wordpress media id or an image object
	 * @returns the description for the $index image of the set
	 * @param int $index the index of the images list
	 */
	protected function get_image_description($index){
		$toret = $this->images[$index];

		if(is_integer($this->images[$index])){
			$post = get_post($this->images[$index]);
			$toret = $post->post_content;
		} elseif(is_object($this->images[$index])){
			$toret = $this->images[$index]->post_content;
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['description']))
				$toret = $this->images[$index]['description'];
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