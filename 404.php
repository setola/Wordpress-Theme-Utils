<?php wp_enqueue_style('page'); ?>
<?php get_header(); ?>
	<div id="main-content" class="container_16">
		<div class="grid_8 alpha">
			<?php echo HtmlHelper::image(get_template_directory_uri().'/images/error_404.gif'); ?>
			<?php the_widget('WP_Widget_Search'); ?>
		</div>
		<div class="grid_8 omega">
			<?php 
				$escape = new EscapeRoute();
				$escape->templates->set_markup('class', 'grid_6');
				echo $escape->get_markup();
			 ?>
		</div>
	</div>
	
	<div class="container_16">
		<div class="grid_8 alpha">
			<?php the_widget('WP_Widget_Pages'); ?>
		</div>
		<div class="grid_8 omega">
			<?php the_widget('WP_Widget_Recent_Posts'); ?>
		</div>
	</div>

<?php get_footer(); ?>
