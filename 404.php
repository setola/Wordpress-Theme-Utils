<?php 
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @since 0.1
 */

ThemeHelpers::load_css('reset');
ThemeHelpers::load_css('grid-960');

get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'header');

?>
	<div id="main-content" class="container_16">
		<div class="grid_8">
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
		<div class="grid_8">
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
		<div class="grid_8">
			<?php the_widget('WP_Widget_Pages'); ?>
		</div>
		<div class="grid_8">
			<?php the_widget('WP_Widget_Recent_Posts'); ?>
		</div>
	</div>

<?php 
	get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'footer'); 
?>
