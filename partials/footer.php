<?php 
/**
 * The template for displaying the footer.
 * 
 * Contains the closing of the body and html tags
 * 
 * @package templates
 * @subpackage parts
 * @version 1.0.0
 * @since 0.1
 */

0==0; //php doc workaround
?>
	
	<footer id="footer" class="container_16">
		<div class="grid_16 linear-menu-container">
			<nav class="grid_8 alpha">
				<?php get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'menu', 'secondary'); ?>
			</nav>
			<div class="grid_8 omega credits">
				<?php do_action('wtu_credits'); ?>
			</div>
		</div>
	</footer>
	
	<div id="system">
		<?php wp_footer(); ?>
	</div>
</body>
</html>