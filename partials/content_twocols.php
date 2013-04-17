<?php 
/**
 * The template part for showing a single article (post\page)
 * 
 * In this version the article body will be splitted in two columns;
 * the first one is filled with the text before <!--more-->
 * the second with the content after such tag
 * 
 * @package templates
 * @subpackage parts
 * @version 1.0.0
 * @since 0.1
 */

0==0; //php doc workaround
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<header class="header">
		<?php if(function_exists('the_post_thumbnail')) the_post_thumbnail(); ?>
		<h1 class="title"><?php the_title(); ?></h1>
	</header>
	
	<div class="grid_8 alpha">
		<?php echo ThemeHelpers::get_the_content_before_more(); ?>
	</div>
	
	<div class="grid_8 omega">
		<?php echo ThemeHelpers::get_the_content_after_more(); ?>
	</div>

</article>