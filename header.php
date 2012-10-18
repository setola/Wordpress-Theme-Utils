<?php the_doctype(); ?>
<?php the_html(); ?>
<?php the_head(); ?>
<body <?php body_class(); ?>>
	<?php the_browse_happy(); ?>
	<div id="head-container" class="container">
		<div id="heading" class="grid_12 heading">
			<?php the_fb_seo(); ?>
		</div>
		<div id="language-menu" class="grid_4">
			<?php the_language_menu(); ?>
		</div>
	</div>
	
	<div id="main-menu" class="container">
		<?php get_template_part('menu'); ?>
	</div>