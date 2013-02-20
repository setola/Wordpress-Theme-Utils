<?php 
/**
 * The Template for displaying the home page
 * 
 * @since 0.1
 */

wp_enqueue_style('front-page');
get_header(); 

?>
	
	<div id="main-container" class="container_16">
		<div id="slideshow" class="grid_16">
			<?php get_template_part('slideshow'); ?>
		</div>
		<div id="page-text" class="grid_10">
			<?php get_template_part('content'); ?>
		</div>
		<div id="sidebar" class="grid_6">
			<?php get_template_part('sidebar'); ?>
		</div>
	</div>
	
<?php get_footer(); ?>
