<?php wp_enqueue_style('page'); ?>
<?php get_header(); ?>

	<div id="main-content" class="container">
		<div class="grid_8 alpha">
			<?php the_404_image(); ?>
			<?php the_widget('WP_Widget_Search'); ?>
		</div>
		<div class="grid_8 omega">
			<?php the_404_escape_route(); ?>
		</div>
	</div>
	
	<div class="container">
		<div class="grid_8 alpha">
			<?php the_widget('WP_Widget_Pages'); ?>
		</div>
		<div class="grid_8 omega">
			<?php the_widget('WP_Widget_Recent_Posts'); ?>
		</div>
	</div>

<?php get_footer(); ?>
