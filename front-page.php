<?php 
/**
 * The Template for displaying the home page
 * @version 1.0.0
 * @package templates
 * @since 0.1
 */

ThemeHelpers::load_css('style');

get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'header');

?>

	<div id="main-container" class="container_16">
		<div id="slideshow-container" class="grid_16 slideshow-container">
			<?php get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'slideshow'); ?>
		</div>
		<div id="page-text" class="grid_10">
			<?php 
				while(have_posts()){
					the_post(); 
					get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'content', get_post_format());
				} 
			?>
		</div>
		<div id="sidebar" class="grid_6">
			<?php get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'sidebar'); ?>
		</div>
	</div>
<?php 
	get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'footer'); 
?>