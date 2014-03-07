<?php 



class BootstrapCarousel extends GalleryHelper{
	public $container_classes;
	
	public function __construct($container_classes='carousel slide'){
		$this->container_classes = $container_classes;
		$this->set_template(<<< EOF

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

EOF
		);
		$this->tpl_slide = <<< EOF
		
	<div class="%class%">
		%image%
		%caption%
	</div>

EOF;
	}
	
	public function get_markup(){
		
		$slides 		=	'';
		$indicators 	=	'';
		$slide_sub = new SubstitutionTemplate();
		$slide_sub->set_tpl($this->tpl_slide);
		
		foreach($this->images as $k => $v){
			$active = ($k==0) ? 'active' : '';
			$caption = '';
			$style = 'width: 100%; height: 100%; background: url(\''.$this->get_image_src($k).'\') no-repeat center center scroll transparent;';
			
			if($this->get_image_title($k)) 
				$caption .= HtmlHelper::standard_tag('h2', $this->get_image_title($k));
			if($this->get_image_caption($k))
				$caption .= HtmlHelper::paragraph($this->get_image_caption($k));
			
			$image = $slide_sub
				->set_markup('class', implode(' ', array('item', $active)))
				->set_markup(
					'image', 
					HtmlHelper::div(
						'', //$this->get_image_src($k), 
						array('alt'=>$this->get_image_alt($k), 'style'=>$style)
					)
				)
				->set_markup('caption', HtmlHelper::div($caption, array('class'=>'carousel-caption')))
				->replace_markup();
			
			$indicator = HtmlHelper::list_item(
				$inner_html, 
				array(
					'data-target'		=>	'#'.$this->unid,
					'data-slide-to'		=>	$k,
					'class'				=>	$active
				)
			);
			
			
			
			$slides 		.=	$image."\n";
			$indicators 	.=	$indicator."\n";
		}
		
		$subs = new SubstitutionTemplate();
		return $subs
			->set_tpl($this->tpl)
			->set_markup('id', $this->unid)
			->set_markup('container_classes', $this->container_classes)
			->set_markup('indicators', HtmlHelper::ordered_list($indicators, array('class'=>'carousel-indicators')))
			->set_markup('slides', HtmlHelper::div($slides,array('class'=>'carousel-inner')))
			->replace_markup();
	}
}



