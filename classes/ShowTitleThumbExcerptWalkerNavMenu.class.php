<?php 

/**
 * Shows the title, the post thumbnail 
 * and the excerpt for every menu entry.
 * @author etessore
 * @version 1.0.0
 */
class ShowTitleThumbExcerptWalkerNavMenu extends Walker_Nav_Menu {
	public $tpl;
	
	public function __construct(){
		$this->tpl = new SubstitutionTemplate();
		$this->tpl->set_tpl(<<< EOF
	<div id="%id%" class="preview-entry">
		<div class="grid_2 alpha">
			<div class="image">%image%</div>
			<div class="permalink">%permalink%</div>
		</div>
		<div class="grid_8 omega">
			<div class="title">%title%</div>
			<div class="body">%body%</div>
		</div>
	</div>
EOF
		);
		
	}
	
	public function end_el(&$output, $item, $depth){
		setup_postdata(get_post($item->object_id));
		$this->tpl
			->set_markup('id', 'post_'.get_the_ID())
			->set_markup('image', get_the_post_thumbnail(get_the_ID(), 'thumbnail'))
			->set_markup('permalink', get_permalink())
			->set_markup('title', get_the_title())
			->set_markup('body', get_the_excerpt());
		
		wp_reset_postdata();
		
		$output .= $this->tpl->replace_markup();
		parent::end_el(&$output, $item, $depth);
	}
}