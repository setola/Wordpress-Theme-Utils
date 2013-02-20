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
 */
wp_enqueue_style('main');
get_header(); 
?>
	
	<div id="main-container">
		<h1>Customize you Default Page by editing index.php</h1>
	</div>
	
<?php get_footer(); ?>
