<?php 
$children = get_pages(
	array(
		'child_of' 		=>	get_the_ID(),
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
			array('class'=>'child-anchor post-'.$child->ID)
		);
	}
	echo HtmlHelper::unorderd_list($list, array('class'=>'children clearfix'));
}
