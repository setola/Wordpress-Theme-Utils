<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<header class="header">
		<?php the_post_thumbnail(); ?>
		<h1 class="title"><?php the_title(); ?></h1>
	</header>
	
	<div class="content">
		<?php the_content(__('Continue reading <span class="meta-nav">&rarr;</span>', 'theme')); ?>
	</div>

</article>