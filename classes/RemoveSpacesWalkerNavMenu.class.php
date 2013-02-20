<?php 
/**
 * contains RemoveSpacesWalkerNavMenu class definition
 */

/**
 * Remove spaces and newlines from the current menu.
 * This is very useful if you want to style a menu with:
 * <code>display: inline-block;
 * text-align: center;</code> 
 * @author etessore
 * @version 1.0.0
 */
class RemoveSpacesWalkerNavMenu extends Walker_Nav_Menu {
	/**
	 * @var array key valuse pair of replacements
	 */
	public $replacements = array("\n"=>'', "\t"=>'');
	
	/**
	 * (non-PHPdoc)
	 * @see Walker_Nav_Menu::start_lvl()
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	public function start_lvl(&$output, $depth){
		parent::start_lvl(&$output, $depth);
		$this->remove_unwanted_chars(&$output);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Walker_Nav_Menu::end_lvl()
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	public function end_lvl(&$output, $depth){
		parent::end_lvl(&$output, $depth);
		$this->remove_unwanted_chars(&$output);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Walker_Nav_Menu::end_el()
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */
	public function end_el(&$output, $item, $depth){
		parent::end_el(&$output, $item, $depth);
		$this->remove_unwanted_chars(&$output);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Walker_Nav_Menu::start_el()
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args other arguments
	 */
	public function start_el(&$output, $item, $depth, $args){
		parent::start_el(&$output, $item, $depth, $args);
		$this->remove_unwanted_chars(&$output);
	}
	
	/**
	 * Removew the unwanted charactes from the given string
	 * @param string $str the string to be escaped
	 * @return string the escaped string
	 */
	private function remove_unwanted_chars(&$str){
		$str = str_replace(
			array_keys($this->replacements), 
			array_values($this->replacements), 
			&$str
		);
		return $str;
	}
}