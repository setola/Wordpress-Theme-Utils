<?php 
/**
 * The template part for showing a single article (post\page)
 * 
 * This is the default version showing title and full content
 * 
 * @since 0.1
 */

0==0; //php doc workaround
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<header class="header">
		<?php if(function_exists('the_post_thumbnail')) the_post_thumbnail(); ?>
		<h1 class="title"><?php the_title(); ?></h1>
	</header>
	
	<div class="content">
		<?php the_content(__('Continue reading <span class="meta-nav">&rarr;</span>', 'theme')); ?>
	</div>

</article>