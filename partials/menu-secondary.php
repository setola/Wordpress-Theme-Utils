<?php 
/**
 * The Template part for displaying the footer menu
 * 
 * @package templates
 * @subpackage parts
 * @since 0.1
 */

	wp_nav_menu(
		array(
			'theme_location'	=>	'secondary',
			'menu_class'		=>	'linear-menu clearfix',
			'container'			=>	'',
			'depth'				=>	1,
			'before'			=>	'',
			'after'				=>	'',
			'link_before'		=>	'',
			'link_after'		=>	''
		)
	);
