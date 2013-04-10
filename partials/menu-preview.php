<?php 
/**
 * Template part to print a title and an excerpt of every menu entry.
 * @package templates
 * @subpackage parts
 * @version 1.0.0
 */
0==0;
?>
<div class="tabs">
<?php 
	wp_nav_menu(
		array(
			'theme_location'	=>	'pages-preview',
			'container'			=>	'',
			'depth'				=>	1,
			'walker'			=>	new HashAnchorWalkerNavMenu(),
			'before'			=>	'',
			'after'				=>	'',
			'link_before'		=>	'',
			'link_after'		=>	''
		)
	);
	
	$locations	= get_nav_menu_locations();
	$menu		= wp_get_nav_menu_object($locations['pages-preview']);
	$menu_items	= wp_get_nav_menu_items($menu->term_id);
	$tpl = new SubstitutionTemplate();
	$tpl->set_tpl(<<< EOF
	<div id="%id%" class="preview-entry clearfix">
		<div class="grid_2 alpha">
			<div class="permalink">%permalink%</div>
		</div>
		<div class="grid_8 omega">
			<div class="title">%title%</div>
			<div class="body">%body%</div>
		</div>
	</div>	
EOF
	);
	
	if(count($menu_items)){
		wp_enqueue_script('tabs');
		foreach($menu_items as $item) {
			global $post;
			$post = get_post($item->object_id);
			setup_postdata($post);
			$tpl
				->set_markup('id', sanitize_title_with_dashes(get_the_title()))
				->set_markup('image', get_the_post_thumbnail(get_the_ID(), 'thumbnail'))
				->set_markup(
					'permalink',
					ThemeHelpers::anchor(
						get_permalink(),
						get_the_post_thumbnail(get_the_ID(), 'thumbnail').__('More Info', 'theme'),
						array('title'=>get_the_title())
					)
				)
				->set_markup('title', get_the_title())
				->set_markup('body', get_the_excerpt());
			echo $tpl->replace_markup();
			wp_reset_postdata();
		}
	}
?>
</div>