<?php 
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @since 0.1
 * @version 1.0.0
 * @package templates
 */

wp_enqueue_style('main');

get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'header');

?>

	<div id="main-container" class="container_16">
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
