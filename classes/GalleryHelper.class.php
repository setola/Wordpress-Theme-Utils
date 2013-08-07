<?php 
/**
 * Stores the GalleryHelper class definition
 */
/**
 * Manages the markup for a generic set of images
 * Extend this with your nice and candy classes!
 * @author etessore
 * @version 1.0.2
 * @package classes
 * @subpackage image manager
 * @todo remove using of FeatureWithAssets
 */
abstract class GalleryHelper /*extends FeatureWithAssets*/{

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
	 * @var int the maximum number of images to show
	 */
	public $image_number;

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
	 * Search the media gallery for suitable images.
	 * 
	 * This is the order:
	 * Check if current post has attached images
	 * Check the default language if has attached images
	 * Check if frontpage has attached images
	 * Check if frontpage has attached images in default language
	 * Build a placeholder
	 */
	public function get_images(){
		$args = array();
		if($this->image_number){
			$args['numberposts'] = $this->image_number;
		}
		
		$this->add_images(self::get_images_from_post($args));// = self::get_images_from_post();
		
		if(!$this->has_images()){
			$this->add_images(self::get_images_from_main_language($args));
		}
		
		if(!$this->has_images() && HotelManager::$enabled){
			$this->add_images(self::get_images_from_closest_hotel($args));
		}
		
		if(!$this->has_images() && HotelManager::$enabled){
			$this->add_images(self::get_images_from_closest_hotel_in_default_language($args));
		}
		
		if(!$this->has_images()){
			$this->add_images(self::get_images_from_frontpage($args));
		}
		
		if(!$this->has_images()){
			$this->add_images(self::get_images_from_homepage_in_default_language($args));
		}
		
		if(!$this->has_images()){
				
			$dimensions = ImageGenerator::get_dimensions($this->media_dimension);
			if($dimensions['width'] == 'unknown') return $this;
			
			$image = new ImageGenerator();
			$image
				->set('width', $dimensions['width'])
				->set('height', $dimensions['height'])
				->set('bg_color', 'cccccc')
				->set('text_color', '222222');
				
			$this->add_image(array(
					'src'	=>	$image->get_image_src(),
					'alt'	=>	$image->get_image_alt(),
					'width'	=>	$image->get_image_width(),
					'height'=>	$image->get_image_height()
			));
		}
		
		return $this;
	}
	
	/**
	 * Check if the current gallery has one or more images
	 * 
	 * Useful to check if jquery.cycle() is needed
	 * 
	 * @return boolean true if there is at least two image
	 */
	public function has_images(){
		return count($this->images) >= 1;
	}
	
	/**
	 * Checks if the current gallery has more no images
	 * @return boolean true if there is no image in the current set
	 */
	public function is_empty(){
		return empty($this->images);
	}
	
	
	/**
	 * Get the images attached to a post
	 * @param array $args
	 * @return Ambigous <multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function get_images_from_post($args=array()){
		$post_id = (!isset($args['post_parent']) || is_null($args['post_parent'])) 
			? get_the_ID() 
			: $args['post_parent'];
		
		
		$defaults = array(
				'post_parent'		=>	$post_id,
				'post_type'			=>	'attachment',
				'post_mime_type'	=>	'image',
				'orderby'			=>	'menu_order',
				'order'				=>	'ASC'
		);
		
		if(function_exists('has_post_thumbnail') && has_post_thumbnail($post_id)){
			$defaults['exclude'] = get_post_thumbnail_id($post_id);
		}
		
		if(taxonomy_exists('media_tag') && isset($args['tax_query'])){
			$args['tax_query'] 	=	wp_parse_args(
				$args['tax_query'],
				array(
					'taxonomy'		=> 'media_tag',
					'field'			=> 'slug',
					'terms'			=> 'slideshow',
					'operator'		=> 'IN'
				)
			);
		}
		
		return get_children(wp_parse_args($args, $defaults));
	}
	
	/**
	 * Get the images from the main language translation of the post
	 * 
	 * Uses WPML to retrieve the post translation in default language
	 * and queries it for attached images.
	 * 
	 * @uses icl_object_id()
	 * @param int $post_id the post id you want to search for
	 * @return Ambigous <Ambigous, multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function get_images_from_main_language($post_id=null){
		$post_id = is_null($post_id) ? get_the_ID() : $post_id;
		
		global $sitepress;
		if(empty($sitepress)) return self::get_images_from_post(array('post_parent' => intval($post_id)));
		
		$post_id = icl_object_id($post_id, get_post_type($post_id), true, $sitepress->get_default_language());
		
		if($post_id == 0) return;
					
		return self::get_images_from_post(array('post_parent'=>intval($post_id)));
	}
	
	/**
	 * Gets images attached to the frontpage
	 */
	public static function get_images_from_frontpage(){
		return self::get_images_from_post(array('post_parent'=>get_option('page_on_front')));
	}
	
	/**
	 * Gets images attached to the frontpage in default language translation 
	 * @return Ambigous <Ambigous, multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function get_images_from_homepage_in_default_language(){
		return self::get_images_from_main_language(get_option('page_on_front'));
	}
	
	/**
	 * Gets images from the first post marked as 'hotel' by HotelManager
	 * @return Ambigous <Ambigous, multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function get_images_from_closest_hotel(){
		return self::get_images_from_post(array('post_parent' => HotelManager::get_hotel_id()));
	}
	
	/**
	 * Gets images from the first post marked as 'hotel' by HotelManager in default language
	 * @return Ambigous <Ambigous, multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function  get_images_from_closest_hotel_in_default_language(){
		return self::get_images_from_post(array('post_parent' => HotelManager::get_hotel_id(null, true)));
	}
	
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
	 * Set the maximum number of images to show
	 * @param int $number the number
	 * @return GalleryHelper $this for chainability
	 */
	public function limit_images_number($number){
		$this->image_number = $number;
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
	 * @param string $key the string has to be substituted 
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
		$this->unid = uniqid('gallery_');
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
	 * Retrieves the path for the given image
	 * If it is an external image it returns the src attributes
	 * @param int $index the index of the images list
	 * @return string path to disc
	 */
	protected function get_image_path($index){
		//return wp_get_attachment_metadata($this->images[$index]);
		
		$toret = $this->images[$index];

		if(is_integer($this->images[$index])){
			//$toret = wp_get_attachment_url($this->images[$index]);
			$toret = get_attached_file($this->images[$index], $this->media_dimension);
		} elseif(is_object($this->images[$index])){
			//$toret = wp_get_attachment_url($this->images[$index]->ID);
			$toret = get_attached_file($this->images[$index]->ID, $this->media_dimension);
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['src']))
				$toret = $this->images[$index]['src'];
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
	 * Retrieves the title for the image with given index
	 * @param int $index the index of the images list
	 * @returns the title attribute for the $index image of the set
	 */
	protected function get_image_title($index){
		$toret = $this->images[$index];

		if(is_integer($this->images[$index])){
			$toret = get_the_title($this->images[$index]);
		} elseif(is_object($this->images[$index])){
			$toret = get_the_title($this->images[$index]->ID);
		} elseif(is_array($this->images[$index])){
			if(isset($this->images[$index]['title']))
				$toret = $this->images[$index]['title'];
		}

		return $toret;
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
			else
				$toret = '';
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
	 * Retrieves the markup for the current gallery
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