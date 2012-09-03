<?php 

/**
 * Adds the level for the current menu element
 * @author etessore
 *
 */
class LevelWalkerNavMenu extends Walker_Nav_Menu {
	public function start_el(&$output, $item, $depth, $args){
		$item->classes[] = 'level-'.$depth;
		return parent::start_el(&$output, $item, $depth, $args);
	}
}