<?php 
/**
 * stores the ShowItemExcerptWalkerNavMenu class definition
 */

/**
 * Shows the title, the post thumbnail 
 * and the excerpt for every menu entry.
 * Additional markup will be appended
 * to the default menu entry.
 * @author etessore
 * @version 1.0.0
 * @package classes
 */
class ShowItemExcerptWalkerNavMenu extends Walker_Nav_Menu {
	/**
	 * @var string the template
	 */
	public $tpl;
	
	/**
	 * @var string the excerpt buffer
	 */
	public $excerpt_output;
	
	/**
	 * Initializes the object with default values.
	 * Hint: overload this if you want 
	 * to change the printed markup
	 */
	public function __construct(){
		$this->tpl = new SubstitutionTemplate();
		$this->tpl->set_tpl(<<< EOF
	<div id="%id%" class="preview-entry">
		<div class="grid_2 alpha">
			<div class="image">%image%</div>
			<div class="permalink">%permalink%</div>
		</div>
		<div class="grid_8 omega">
			<div class="title">%title%</div>
			<div class="body">%body%</div>
		</div>
	</div>
EOF
		);
		$this->excerpt_output = '';
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Walker_Nav_Menu::start_el()
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $args
	 */
	function start_el(&$output, $item, $depth, $args){
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
		$excerpt_id = 'navmenu-excerpt-'.$item->ID;

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( '#'.$excerpt_id   ) .'"' : '';
		$attributes .= 'data-open="'.$excerpt_id.'"';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;
		
		global $post;
		$post = get_post($item->object_id);
		setup_postdata($post);
		$this->tpl
			->set_markup('id', $excerpt_id)
			->set_markup('image', get_the_post_thumbnail(get_the_ID(), 'thumbnail'))
			->set_markup(
				'permalink', 
				ThemeHelpers::anchor(
					get_permalink(), 
					get_the_title(), 
					array('title'=>get_the_title())
				)
			)
			->set_markup('title', get_the_title())
			->set_markup('body', get_the_excerpt());
		$this->excerpt_output .= $this->tpl->replace_markup();
		wp_reset_postdata();

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		$output .= $this->tpl->replace_markup();
	}
}