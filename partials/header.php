<?php 
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till 'main container' div
 *
 * @package templates
 * @subpackage parts
 * @version 1.0.0
 * @since 0.1
 */

echo HtmlHelper::doctype('html5');
echo HtmlHelper::open_html();
ThemeHelpers::load_css('reset');
ThemeHelpers::load_css('grid-960');

?>
<head>
	<?php 
		$header = new HeadHelper();
		$header
			->set_title(ThemeHelpers::get_the_seo_title())
			->set_meta_tag(
				array(
					'name'		=>	'description',
					'content'	=>	ThemeHelpers::get_the_seo_description()
				)
			)
			->the_head();
		wp_head();
	?>
</head>
<body <?php body_class(); ?>>
	<div id="head-container" class="container_16">
		<header id="heading" class="grid_12 heading">
			<h1><?php echo ThemeHelpers::get_the_seo_h1(); ?></h1>
			<span><?php echo ThemeHelpers::get_the_seo_span(); ?></span>
		</header>
		<?php global $sitepress; if($sitepress) : ?>
		<div id="language-menu" class="grid_4">
			<?php do_action('icl_language_selector'); ?>
		</div>
		<?php endif; ?>
	</div>
	
	<div id="main-menu" class="container_16">
		<div class="linear-menu-container grid_16">
			<?php get_template_part(WORDPRESS_THEME_UTILS_PARTIALS_RELATIVE_PATH.'menu'); ?>
		</div>
	</div>