<article id="post-<?php the_ID(); ?>" <?php post_class('open-details'); ?>>
	
	<header class="header">
		<?php the_post_thumbnail(); ?>
		<h1 class="title"><?php the_title(); ?></h1>
	</header>
	
	<?php if(ThemeHelpers::has_more_tag()): 
			wp_enqueue_style('open-details');
			wp_enqueue_script('open-details');
	?>
	<div class="excerpt">
		<?php echo ThemeHelpers::get_the_content_before_more(__('Continue reading <span class="meta-nav">&rarr;</span>', 'theme')); ?>
	</div>
	
	<div class="content openclose">
		<?php echo ThemeHelpers::get_the_content_after_more(__('Continue reading <span class="meta-nav">&rarr;</span>', 'theme')); ?>
	</div>
	
	<div class="buttons">
		<a 
			data-id="page-content-openclose" 
			data-open="<?php _e('More Info', 'theme'); ?>" 
			data-close="<?php _e('Close', 'theme'); ?>" 
			class="open-details-toggler" 
			href="javascript:;"
		><?php _e('More Info', 'theme'); ?></a>
	</div>
	<?php else: ?>
	<div class="content">
		<?php the_content(__('Continue reading <span class="meta-nav">&rarr;</span>', 'theme')); ?>
	</div>
	<?php endif; ?>

</article>