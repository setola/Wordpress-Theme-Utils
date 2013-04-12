<?php 
/**
 * The Template for displaying the navigation bar
 * 
 * @package templates
 * @subpackage parts
 * @since 0.1
 */

	ThemeHelpers::load_css('linear-menu');
	wp_nav_menu(
		array(
			'theme_location'	=>	'primary',
			'menu_class'		=>	'linear-menu clearfix',
			'container'			=>	'',
			'depth'				=>	1,
			//'walker'			=>	new RemoveSpacesWalkerNavMenu(),
			'before'			=>	'',
			'after'				=>	'',
			'link_before'		=>	'',
			'link_after'		=>	''
		)
	);