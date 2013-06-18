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
	
	public $classes = array();
	
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
			<div class="excerpt">%excerpt%</div>
		</div>
	</div>
EOF
		);
		$this->excerpt_output = '';
		
	}
	
	/**
	 * Builds the %image% substitution
	 * @param int $post_id the post ID
	 * @param string $size the media size
	 * @param array $args optional arguments for the anchor
	 * @return string the image markup
	 */
	public function get_image($item, $size, $args){
		return HtmlHelper::anchor(
			get_permalink($item->object_id), 
			get_the_post_thumbnail($item->object_id, $size), 
			$args
		);
	}
	
	/**
	 * Builds the %id% substitution
	 * @param int $post_id the post ID
	 * @return string the id
	 */
	public function get_excerpt_id($item){
		return 'navmenu-excerpt-'.$item->object_id;
	}
	
	/**
	 * Builds the id of the html li element
	 * @param object $item the menu item
	 * @param array $args additional arguments
	 * @return string the id
	 */
	public function get_container_id($item, $args){
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
		return $id;
	}
	
	/**
	 * Builds the %permalink% substitution
	 * @param int $post_id the post ID
	 * @param array $args optional arguments for the anchor tag
	 * @return string html anchor tag for the permalink
	 */
	public function get_permalink($item, $args=array()){
		return HtmlHelper::anchor(
			get_permalink($item->object_id), 
			$this->get_title($item), 
			$args
		);
	}
	
	/**
	 * Builds the %title% substitution
	 * @param int $post_id the post ID
	 * @return Ambigous <string, mixed> the title
	 */
	public function get_title($item){		 
		return apply_filters( 'the_title', $item->title, $item->ID );		
	}
	
	/**
	 * Builds the %excerpt% substitution
	 * @param int $post_id the post ID
	 * @return Ambigous <string, mixed> the excerpt
	 */
	public function get_excerpt($item){
		global $post;
		$post = get_post($item->object_id);
		return get_the_excerpt();
	}
	
	/**
	 * Builds the %href% substitution
	 * @param int $post_id the post ID
	 * @return string the href for the current menu element
	 */
	public function get_href($item){
		if(isset($item->object_id)) return get_permalink($item->object_id);
	}
	
	/**
	 * Adds a class to the li
	 * @param string $class the class
	 * @return ShowItemExcerptWalkerNavMenu $this for chainability
	 */
	public function add_class($class){
		$this->classes[] = $class;
		return $this;
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
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$this->classes = array_merge((array) $item->classes, $this->classes);
		$this->add_class('menu-item-' . $item->ID);

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $this->classes ), $item, $args ) );

		$output .= $indent . '<li' . $this->get_container_id($item, $args) . ' class="' . esc_attr( $class_names ) . '"' .'>';
		
		$this->tpl
			->set_markup('id', 			$this->get_excerpt_id($item))
			->set_markup('image', 		$this->get_image($item, 'thumbnail', array('title'=>$this->get_title($item))))
			->set_markup('permalink', 	$this->get_permalink($item, array('title'=>$this->get_title($item))))
			->set_markup('title', 		$this->get_title($item))
			->set_markup('excerpt', 	$this->get_excerpt($item))
			->set_markup('href',		$this->get_href($item));		
		$this->excerpt_output .= $this->tpl->replace_markup();

		$output .= apply_filters( 'walker_nav_menu_start_el', $this->tpl->replace_markup(), $item, $depth, $args );
	}
}