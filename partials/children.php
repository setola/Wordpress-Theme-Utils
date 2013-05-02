<?php 
/**
 * Template part for listing child pages with post thumbnail and excerpt
 * 
 * @package templates
 * @subpackage parts
 * @version 1.0.0
 * @since 0.1
 */

$children = get_pages(
	array(
		'child_of' 		=>	get_the_ID(),
		'sort_column' 	=>	'menu_order',
		'sort_order' 	=>	'desc'
	)
);

if(count($children)){
	$subs = new SubstitutionTemplate();
	$subs->set_tpl(<<<EOF
<div class="child clearfix">
	<div class="image grid_4 alpha">%image%</div>
	<div class="grid_8 omega">
		<div class="title">%title%</div>
		<div class="excerpt">%excerpt%</div>
		<div class="buttons clearfix">%moreinfo%%book%</div>
	</div>
</div>
EOF
	);
	
	$list = array();
	global $post;
	foreach($children as $post){
		setup_postdata($post);
		echo $subs
			->set_markup('image', HtmlHelper::anchor(get_permalink(), get_the_post_thumbnail()))
			->set_markup('title', HtmlHelper::anchor(get_permalink(), get_the_title()))
			->set_markup('excerpt', get_the_excerpt())
			->set_markup('moreinfo', '<div class="more-container">'.HtmlHelper::anchor(get_permalink(), __('More Info', 'theme')).'</div>')
			->set_markup('book', '<div class="cta-container">'.HtmlHelper::anchor('javascript:;', __('Book This Room', 'theme'), array('class'=>'book-room cta')).'</div>')
			->replace_markup();
	}
	wp_reset_postdata();
}
