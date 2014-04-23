<?php 
/**
 * Manages the placeholder for various responsive design breakpoints.
 * 
 * @author etessore
 * @version 1.0.0
 */
class ResponsiveImagesManager{
	public static $instance = null;
	public static $url = '';
	protected $breakpoints;
	protected $safe_mode = true;
		
	private function __construct(){
		//add_filter('wp_get_attachment_url', array(__CLASS__, 'on_wp_get_attachment_url'), 99, 2);
		if(!is_admin()) add_filter('wp_footer', array(&$this, 'on_print_footer_scripts'), 99, 2);
		add_action('wp_ajax_responsive_image', array(&$this, 'on_ajax_responsive_image'));
		add_action('wp_ajax_nopriv_responsive_image', array(&$this, 'on_ajax_responsive_image'));
		add_action('wp_ajax_responsive_background', array(&$this, 'on_ajax_responsive_background'));
		add_action('wp_ajax_nopriv_responsive_background', array(&$this, 'on_ajax_responsive_background'));
		
		$this
			->set_breakpoint('xs', 0, 480)
			->set_breakpoint('sm', 481, 768)
			->set_breakpoint('md', 769, 992)
			->set_breakpoint('lg', 993, INF);
	}
	
	/**
	 * Retrieves the singleton instance
	 * @return ResponsiveImagesManager
	 */
	public static function get_instance(){
		if(is_null(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Set the single breakpoint to the given parameters
	 * @param string $name breakpoint neme, will be used for media size suffix
	 * @param int $min the minum width for this breakpoint
	 * @param int $max the maximum width for this breakpoint
	 * @return ResponsiveImagesManager $this for chainability
	 */
	public function set_breakpoint($name, $min, $max){
		$this->breakpoints[$name] = array($min, $max);
		return $this;
	}
	
	public function set_mode($mode){
		$this->safe_mode = $mode;
		return $this;
	}
	
	/**
	 * Retrieves some image attributes
	 * @param int $attachment_id
	 * @param string $size
	 * @return multitype:string unknown
	 */
	protected function get_image($attachment_id, $size){
		$attachment = get_post($attachment_id);
		$default_attr = array(
				'class'				=> "attachment-$size",
				'data-image-size'	=>	$size,
				'data-image-id'		=>	$attachment_id,
				'data-image-alt'	=> 	trim(strip_tags( get_post_meta($attachment_id, '_wp_attachment_image_alt', true) )), // Use Alt field first
		);
		if ( empty($default_attr['data-image-alt']) )
			$default_attr['data-image-alt'] = trim(strip_tags( $attachment->post_excerpt )); // If not, Use the Caption
		if ( empty($default_attr['data-image-alt']) )
			$default_attr['data-image-alt'] = trim(strip_tags( $attachment->post_title )); // Finally, use the title

		return $default_attr;
	}
	
	protected function get_suitable_breakpoint($width){
		$k = '';
		foreach($this->breakpoints as $k => $breakpoint){
			if($width > $breakpoint[0] && $width < $breakpoint[1]){
				return $k;
			}
		}
		
		return $k;
	}
	
	/**
	 * Retrieves the active breakpoints
	 * @return array list of breakpoints
	 */
	public function get_breakpoints(){
		return $this->breakpoints;
	}
	
	/**
	 * Returns the markup for an element with adaptive image background
	 * @param int $attachment_id the image id
	 * @param string $size the image size
	 * @param string $tag the html tag
	 * @param array $attr html tag attributes
	 * @return string html markup
	 */
	public function background_image($attachment_id, $size, $tag='div', $attr=array()){
		$default_attr = self::get_image($attachment_id, $size);
		@$attr['class'] = $default_attr['class'] . ' responsive-image-background ' . $attr['class'];
		return HtmlHelper::standard_tag($tag, '', array_merge($default_attr, $attr));
	}
	
	/**
	 * Returns the markup for an adaptive image
	 * @param int $attachment_id the iamge id
	 * @param string $size the image size
	 * @param array $attr img tag attributes
	 * @return string html merkup
	 */
	public function image($attachment_id, $size, $attr=array()){
		$default_attr = self::get_image($attachment_id, $size);
		@$attr['class'] = $default_attr['class'] . ' responsive-image-placeholder ' . $attr['class'];
		return
			HtmlHelper::span('', $default_attr)
			.HtmlHelper::standard_tag('noscript', wp_get_attachment_image($imageid, $size, false, $attr));
	}
	
	public function on_ajax_responsive_image(){
		$image_id = intval($_REQUEST['id']);
		$size = sanitize_title($_REQUEST['size']);
		$width = intval($_REQUEST['w']);
		
		$path = wp_upload_dir();
		$suitable_size =  $size.'-'.$this->get_suitable_breakpoint($width);
		$image = image_get_intermediate_size($image_id, $suitable_size);
		$mime_type = '';
		
		
		if($image){
			$image_path = $path['basedir'] . '/' . $image['path'];
			$mime_type = $image['mime-type'];
		} elseif($this->safe_mode) {
			$image = wp_get_attachment_metadata($image_id);
			$mime_type = get_post_mime_type($image_id);
			$image_path = $path['basedir'] . '/' . $image['file'];
		}
		
		
		if(file_exists($image_path)){
			header('Content-Type: ' . $mime_type);
			header('Content-Length: ' . filesize($image_path));
			readfile($image_path);
		} else {
			header("HTTP/1.0 404 Not Found");
			wp_die(sprintf('The media size "%s" for the image with id %s does not exist', $suitable_size, $image_id));
		}
		
		die();
	}
	
	public function on_ajax_responsive_background(){
		$image_id = intval($_REQUEST['id']);
		$size = sanitize_title($_REQUEST['size']);
		$width = intval($_REQUEST['w']);
		
		
		$path = wp_upload_dir();
		$suitable_size =  $size.'-'.$this->get_suitable_breakpoint($width);
		
		
		die(wp_get_attachment_image_src($image_id, $suitable_size));
		
	}
		
	public function on_print_footer_scripts(){
	?>
		<script id="responsive-image-manager-feature" type="text/javascript">
			var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
			<?php /* encoding problem for INF: http://stackoverflow.com/questions/9124942/what-does-double-inf-mean-in-php-warning */?>
			var breakpoints = <?php echo @json_encode($this->get_breakpoints()); ?>;
			<?php /* and pratical solution */ ?>
			jQuery.each(breakpoints, function(index, value){ if(value[1] == 0) value[1] = Infinity; });
			var currentBreakPoint = locateBreakpoint();
			
			function encodeQueryData(data){
			   var ret = [];
			   for (var d in data)
			      ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
			   return ret.join("&");
			}

			function locateBreakpoint(){
				var w = jQuery(document).width();
				var point;
				jQuery.each(breakpoints, function(index, value){
					if(value[0] < w && w < value[1]){
						point = index;
						return;
					}
				});
				return point;
			}

			function renderResponsiveImages(){
				jQuery('.responsive-image-placeholder:visible').each(function(){
					var placeholder = jQuery(this);
					Query(
						'<img>', 
						{
							"alt":placeholder.data('image-alt'),
							"src":ajaxurl+'?'+encodeQueryData({
								"action"	:	"responsive_image",
								"id"		:	placeholder.data('image-id'),
								"w"			:	jQuery(document).width(),
								"size"		:	placeholder.data('image-size')
							})
						}
					).load(function(){
						placeholder.replaceWith(jQuery(this));
					});
				});
				jQuery('.responsive-image-background').each(function(){
					var placeholder = jQuery(this);
					var imageURI = ajaxurl+'?'+encodeQueryData({
						"action"	:	"responsive_image",
						"id"		:	placeholder.data('image-id'),
						"w"			:	jQuery(document).width(),
						"size"		:	placeholder.data('image-size')
					});
					jQuery('<img>', {src:imageURI}).load(function(){
						placeholder.css(
							'background-image', 
							'url(' + imageURI + ')'
						);
					});
				});
			}
			
			jQuery(document).ready(renderResponsiveImages);
			jQuery(window).resize(function(){
				var newBreakPoint = locateBreakpoint();
				if(currentBreakPoint != newBreakPoint){
					currentBreakPoint = newBreakPoint
					renderResponsiveImages();
				}
			});
		</script>
		<?php 
	}

}