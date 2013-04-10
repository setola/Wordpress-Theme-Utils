<?php 
/**
 * The Template part for displaying the list of child pages
 * 
 * @package templates
 * @subpackage parts
 * @version 1.0.0
 * @since 0.1
 */

global $post;

$children = get_pages(
	array(
		'child_of' 		=>	$post->post_parent,
		'sort_column' 	=>	'menu_order',
		'sort_order' 	=>	'desc'
	)
);

if(count($children)){
	$list = array();
	foreach($children as $child){
		$list[] = HtmlHelper::anchor(
			get_permalink($child->ID), 
			get_the_title($child->ID),
			array('class'=>'brother-anchor post-'.$child->ID.(($child->ID == get_the_ID())?' current':''))
		);
	}
	echo HtmlHelper::unorderd_list($list, array('class'=>'brothers clearfix'));
}
