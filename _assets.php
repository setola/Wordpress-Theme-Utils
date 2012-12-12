<?php 
/**
 * Register here your theme assets, only for the frontend!
 * @deprecated use DefaultAssets class instead
 */
if(false && !is_admin()){
	
	/**
	 * Jquery UI replacement for the conflicting simlinked js in the wp installation
	 */
	wp_register_script('jquery', get_template_directory_uri().'/js/jquery.min.js', null, '1.7.2', true);
	wp_register_script('jquery-ui-core', get_template_directory_uri().'/js/jquery.ui.core.min.js', array('jquery'), '1.8.20', true);
	wp_register_script('jquery.ui.selectmenu', get_template_directory_uri().'/js/jquery.ui.selectmenu.js', array('jquery','jquery-ui-core','jquery-ui-position','jquery-ui-widget'), '1.2.1', true);
	wp_register_script('jquery-ui-position', get_template_directory_uri().'/js/jquery.ui.position.min.js', array('jquery','jquery-ui-core'), '1.8.20', true);
	wp_register_script('jquery-ui-widget', get_template_directory_uri().'/js/jquery.ui.widget.min.js', array('jquery','jquery-ui-core'), '1.8.20', true);
	
	
	/**
	 * Some usefull libraries
	 */
	wp_register_script('jquery.imagesloaded', get_template_directory_uri().'/js/jquery.imagesloaded.js', array('jquery'), '2.0.1', true);
	wp_register_script('jquery.cycle', get_template_directory_uri().'/js/jquery.cycle.js', array('jquery'), '2.0.1', true);
	wp_register_script('jquery.scrollto', get_template_directory_uri().'/js/jquery.scrollTo.js', array('jquery'), '1.4.2', true);
	wp_register_script('jquery-fancybox', get_template_directory_uri().'/js/jquery.fancybox.js', array('jquery'), '2.1.0', true);
	
	
	
	/**
	 * Initialization scripts
	 */
	wp_register_script('slideshow', get_template_directory_uri().'/js/slideshow.js', array('jquery.imagesloaded', 'jquery.cycle', 'jquery.scrollto'), '0.1', true);
	wp_register_script('slideshow-fullscreen', get_template_directory_uri().'/js/slideshow-fullscreen.js', array('jquery.imagesloaded', 'jquery.cycle', 'jquery.scrollto'), '0.1', true);
	wp_register_script('social', get_template_directory_uri().'/js/social.js', array('jquery'), '0.1', true);
	wp_register_script('cycle', get_template_directory_uri().'/js/cycle.js', array('jquery.imagesloaded', 'jquery.cycle'), '0.1', true);
	wp_register_script('crs', get_template_directory_uri().'/js/crs.js', array('jquery'), '0.1', true);
	wp_register_script('open-details', get_template_directory_uri().'/js/open-details.js', array('jquery'), '0.1', true);
	wp_register_script('navbar-fixed', get_template_directory_uri().'/js/navbar-fixed.js', array('jquery'), '0.1', true);
	wp_register_script('photogallery', get_template_directory_uri().'/js/photogallery.js', array('jquery','jquery-fancybox'), '0.1', true);
	wp_register_script('modernizr', get_template_directory_uri().'/html5-boilerplate/js/vendor/modernizr-2.6.1.min.js', null, '2.6.1', false);
	
	
	/**
	 * Fblib
	 */
	wp_register_script('fbqs', 'http://static.fbwebprogram.com/fbcdn/fastqs/fastbooking_loader.php?v=1&callbackScriptURL='.get_template_directory_uri().'/js/fbcallback.js', array('jquery','jquery.ui.selectmenu'), '1', true);


	
	/**
	 * Google Map
	 */
	wp_register_script('gmaps.api', 'http://maps.google.com/maps/api/js?sensor=false', null, '3', 'screen');
	wp_register_script('map', get_template_directory_uri().'/js/gmap.js', array('jquery','gmaps.api'), '0.1', 'screen');
	wp_register_script('map-directions', get_template_directory_uri().'/js/map-directions.js', array('jquery','map'), '0.1', true);



	/**
	 * HTML5 Boiler Plate
	 */
	wp_register_style('h5bp.normalize', get_template_directory_uri().'/html5-boilerplate/css/normalize.css', null, '1.0.1', 'screen');
	wp_register_style('h5bp.main', get_template_directory_uri().'/html5-boilerplate/css/main.css', null, '1.0.1', 'screen');
	
	
	
	/**
	 * Some Web Fonts
	 */
	wp_register_style('open-sans-cl', 'http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300', null, '0.1', 'screen');
	
	
	
	/**
	 * Theme Specific CSS for part of page
	 */
	wp_register_style('reset', get_template_directory_uri().'/css/reset.css', null, '2.0', 'screen');
	wp_register_style('sprite', get_template_directory_uri().'/css/sprite.css', null, '0.1', 'screen');
	wp_register_style('grid-960', get_template_directory_uri().'/css/grid-960.css', null, '0.1', 'screen');
	wp_register_style('fbqs', get_template_directory_uri().'/css/fbqs.css', null, '0.1', 'screen');
	wp_register_style('standard-style', get_template_directory_uri().'/css/standard-style.css', null, '0.1', 'screen');
	wp_register_style('room', get_template_directory_uri().'/css/room.css', null, '0.1', 'screen');
	wp_register_style('offers', get_template_directory_uri().'/css/offers.css', null, '0.1', 'screen');
	wp_register_style('location', get_template_directory_uri().'/css/location.css', null, '0.1', 'screen');
	wp_register_style('photogallery', get_template_directory_uri().'/css/photogallery.css', null, '0.1', 'screen');
	wp_register_style('open-details', get_template_directory_uri().'/css/open-details.css', null, '0.1', 'screen');
	wp_register_style('jquery-fancybox', get_template_directory_uri().'/css/jquery.fancybox.css', null, '2.1.0', 'screen');
	
	
	/**
	 * Theme Specific CSS for entire page
	 */
	wp_register_style('front-page', get_template_directory_uri().'/css/front-page.css', array('reset', 'grid-960', 'sprite', 'fbqs', 'standard-style'), '0.1', 'screen');
	wp_register_style('page', get_template_directory_uri().'/css/page.css', array('reset', 'grid-960', 'sprite', 'fbqs', 'standard-style'), '0.1', 'screen');

}

