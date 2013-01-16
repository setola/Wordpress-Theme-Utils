<?php wp_enqueue_style('front-page'); ?>
<?php get_header(); ?>
	
	<div id="main-container" class="container">
		<div id="slideshow">
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
