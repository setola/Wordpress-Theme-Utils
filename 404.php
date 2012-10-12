<?php wp_enqueue_style('main'); ?>
<?php get_header(); ?>

<div id="main-container">
	<div class="overflow-shadows clearfix">
		<div id="main-content" class="clarfix two-cols w940 content">
			<div class="right column">
				<?php the_404_image(); ?>
			</div>
			<div class="left column">
				<?php the_404_escape_route(); ?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>
