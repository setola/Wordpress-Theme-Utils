<?php wp_enqueue_style('page'); ?>
<?php get_header(); ?>
	<div id="main-content" class="container_16">
		<div class="grid_8 alpha">
			<?php 
				$image = new ImageGenerator(); 
				$image
					->set('width', '460')
					->set('height', '300')
					->set('image_text', '404')
					->set('font_size', '150')
					->set('text_position_x', '80')
					->set('text_position_y', '200')
					->set('bg_color', 'cccccc')
					->set('text_color', '222222');
				echo $image->get_markup();			
			?>
		</div>
		<div class="grid_8 omega">
			<?php 
				$escape = new EscapeRoute();
				$escape->templates->set_markup('class', 'grid_6');
				echo $escape->get_markup();
			 ?>
			<hr>
			<?php the_widget('WP_Widget_Search'); ?>
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
