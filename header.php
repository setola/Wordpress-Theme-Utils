<?php echo HtmlHelper::doctype('html5'); ?>
<?php the_html(); ?>
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
			<?php the_fb_seo(); ?>
		</div>
		<div id="language-menu" class="grid_4">
			<?php the_language_menu(); ?>
		</div>
	</div>
	