<?php 
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till 'main container' div
 *
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
		<div id="heading" class="grid_12 heading">
			<h1><?php echo ThemeHelpers::get_the_seo_h1(); ?></h1>
			<span><?php echo ThemeHelpers::get_the_seo_span(); ?></span>
		</div>
		<div id="language-menu" class="grid_4">
			<?php do_action('icl_language_selector'); ?>
		</div>
	</div>
	
	<div id="main-menu" class="container_16">
		<?php get_template_part('menu'); ?>
	</div>