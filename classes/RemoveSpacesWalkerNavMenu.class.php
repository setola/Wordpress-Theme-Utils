<?php 

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
	
	public function start_lvl(&$output, $depth){
		parent::start_lvl(&$output, $depth);
		$this->remove_unwanted_chars(&$output);
	}
	
	public function end_lvl(&$output, $depth){
		parent::end_lvl(&$output, $depth);
		$this->remove_unwanted_chars(&$output);
	}
	
	public function end_el(&$output, $item, $depth){
		parent::end_el(&$output, $item, $depth);
		$this->remove_unwanted_chars(&$output);
	}
	
	public function start_el(&$output, $item, $depth, $args){
		parent::start_el(&$output, $item, $depth, $args);
		$this->remove_unwanted_chars(&$output);
	}
	
	private function remove_unwanted_chars(&$str){
		$str = str_replace(
			array_keys($this->replacements), 
			array_values($this->replacements), 
			&$str
		);
		return $str;
	}
}