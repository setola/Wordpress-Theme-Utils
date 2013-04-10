<?php 
/**
 * stores the LevelWalkerNavMenu class definition
 */

/**
 * Adds the level for the current menu element
 * @author etessore
 * @version 1.0.0
 * @package classes
 *
 */
class LevelWalkerNavMenu extends Walker_Nav_Menu {
	
	/**
	 * (non-PHPdoc)
	 * @see Walker_Nav_Menu::start_el()
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	public function start_el(&$output, $item, $depth, $args){
		$item->classes[] = 'level-'.$depth;
		return parent::start_el(&$output, $item, $depth, $args);
	}
}