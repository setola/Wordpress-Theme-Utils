<?php wp_enqueue_style('front-page'); ?>
<?php get_header(); ?>
	
	<div id="main-container" class="container">
		<div id="slideshow">
			<?php the_slideshow(); ?>
		</div>
		<div id="front-page-text">
			<div class="title">
				<?php the_logo(); ?>
			</div>
			<div class="body">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	
<?php get_footer(); ?>
