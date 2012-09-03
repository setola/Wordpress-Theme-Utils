<?php 

class PhotogalleryThumbWithFancybox extends GalleryHelper{
	public $sizes;

	public function __construct(){
		$this
		->set_markup('loading', '<div class="loading">'.__('Loading...', $this->textdomain).'</div>')
		->set_sizes(array('w'=>220,'h'=>150))
		->set_template(<<< EOF
	<div id="%gallery-id%" class="gallery">
		<div class="big-image-container">
			%images%
		</div>
	</div>
EOF
		);
	}
	
	public function set_sizes($sizes){
		$this->sizes = $sizes;
		return $this;
	}
	
	public function load_assets(){
		wp_enqueue_style('photogallery');
		wp_enqueue_script('photogallery');
	}
	
	public function get_markup(){
		$markup_images = '';
		if(count($this->images)>0){
			$this->load_assets();
			foreach ($this->images as $k => $image){
				$classes = array('grid_4');
				if($k%4==0 && $classes[] = 'alpha');
				if($k%3==0 && $classes[] = 'omega');
				
				$markup_images .= 
					'<div class="'.implode(' ', $classes).'">'.
					ThemeHelpers::anchor(
						$this->get_image_src($k), 
						ThemeHelpers::image(
							$this->get_image_src($k)
							.'?'
							.http_build_query($this->sizes)
						),
						'rel="group" class="fancy"'
					)
					.'</div>';
			}
		}
	
		if(!$this->unid){
			$this->calculate_unique();
		}
	
		$this->static_markup['gallery-id'] = $this->unid;
		$this->static_markup['images'] = $markup_images;
	
		return str_replace(
				array_map(create_function('$k', 'return "%".$k."%";'), array_keys($this->static_markup)),
				array_values($this->static_markup),
				$this->tpl
		);
	}
}