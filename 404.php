<?php wp_enqueue_style('page'); ?>
<?php get_header(); ?>

<div id="main-content" class="container">
	<div class="grid_6 alpha">
		<?php the_404_image(); ?>
	</div>
	<div class="grid_6 omega">
		<?php the_404_escape_route(); ?>
	</div>
</div>

<?php get_footer(); ?>
