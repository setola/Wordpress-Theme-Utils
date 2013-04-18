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
abstract class GalleryHelper extends FeatureWithAssets{

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
		$this->images = GalleryHelper::get_images_from_post();
		
		if(empty($this->images)){
			$this->images = GalleryHelper::get_images_from_main_language();
		}
		
		if(empty($this->images)){
			$this->images = GalleryHelper::get_images_from_frontpage();
		}
		
		if(empty($this->images)){
			$this->images = GalleryHelper::get_images_from_homepage_in_default_language();
		}
		
		if(empty($this->images)){
				
			$dimensions = ImageGenerator::get_dimensions('slideshow');
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
	 * Get the images attached to a post
	 * @param array $args
	 * @return Ambigous <multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function get_images_from_post($args=array()){
		$post_id = is_null($args['post_parent']) ? $post_id : get_the_ID();
		
		$defaults = array(
				'post_parent'		=> $post_id,
				'post_type'			=> 'attachment',
				'post_mime_type'	=> 'image',
		);
		
		if(taxonomy_exists('media_tag')){
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
	 * @param string $post_id the post id you want to search for
	 * @return Ambigous <Ambigous, multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function get_images_from_main_language($post_id=null){
		global $sitepress;
		if(empty($sitepress)) return self::get_images_from_post($post_id);
		
		return self::get_images(
			array(
				'post_parent'=>icl_object_id(
					is_null($post_id) ? $post_id : get_the_ID(), 
					'post', 
					true, 
					$sitepress->get_default_language()
				)
			)
		);
	}
	
	/**
	 * Get images attached to the frontpage
	 */
	public static function get_images_from_frontpage(){
		return self::get_images(array('post_parent'=>get_option('page_on_front')));
	}
	
	/**
	 * Get images attached to the frontpage in default language translation 
	 * @return Ambigous <Ambigous, multitype:, boolean, multitype:Ambigous <NULL> >
	 */
	public static function get_images_from_homepage_in_default_language(){
		return self::get_images_from_main_language(array('post_parent'=>get_option('page_on_front')));
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