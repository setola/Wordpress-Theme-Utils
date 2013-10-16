<?php 



class BootstrapCarousel extends GalleryHelper{
	public $container_classes;
	
	public function __construct($container_classes='carousel slide'){
		$this->container_classes = $container_classes;
	}
	
	public function get_markup(){
		$tpl = <<< EOF

<div id="%id%" class="%container_classes%">
	<!-- Indicators -->
	%indicators%
	
	<!-- Wrapper for slides -->
	%slides%
	
	<!-- Controls -->
	<a class="left carousel-control" href="#%id%" data-slide="prev">
		<span class="icon-prev"></span>
	</a>
	<a class="right carousel-control" href="#%id%" data-slide="next">
		<span class="icon-next"></span>
	</a>
</div>

EOF;
		$tpl_slide = <<< EOF
		
	<div class="%class%">
		%image%
		%caption%
	</div>

EOF;
		
		$slides 		=	'';
		$indicators 	=	'';
		$slide_sub = new SubstitutionTemplate();
		$slide_sub->set_tpl($tpl_slide);
		
		foreach($this->images as $k => $v){
			$caption = '';
			if($this->get_image_title($k)) 
				$caption .= HtmlHelper::standard_tag('h2', $this->get_image_title($k));
			if($this->get_image_description($k))
				$caption .= HtmlHelper::paragraph($this->get_image_description($k));
			
			$image = $slide_sub
				->set_markup('class', ($k==0) ? 'item active' : 'item')
				->set_markup(
					'image', 
					HtmlHelper::image(
						$this->get_image_src($k), 
						array('alt'=>$this->get_image_alt($k))
					)
				)
				->set_markup('caption', HtmlHelper::div($caption, array('class'=>'carousel-caption')))
				->replace_markup();
			
			$indicator = HtmlHelper::list_item(
				$inner_html, 
				array(
					'data-target'		=>	'#'.$this->unid,
					'data-slide-to'		=>	$k
				)
			);
			
			
			
			$slides 		.=	$image."\n";
			$indicators 	.=	$indicator."\n";
		}
		
		$subs = new SubstitutionTemplate();
		return $subs
			->set_tpl($tpl)
			->set_markup('id', $this->unid)
			->set_markup('container_classes', $this->container_classes)
			->set_markup('indicators', HtmlHelper::ordered_list($indicators, array('class'=>'carousel-indicators')))
			->set_markup('slides', HtmlHelper::div($slides,array('class'=>'carousel-inner')))
			->replace_markup();
	}
}



